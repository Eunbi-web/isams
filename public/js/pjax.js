/*!
 * ISAMS PJAX — pushState + AJAX navigation.
 *
 * Menu/link clicks no longer reload the whole page: the layout shell
 * (sidebar, topbar frame, theme, chatbot, loaded CSS/JS) stays alive and only
 * the data area is swapped in from the fetched page. On slow connections a
 * prefetch starts while the user is still hovering the menu item, so the next
 * page can render instantly.
 *
 * Pages opt in by having <body data-pjax-layout="..."> and a
 * <div id="pjax-container"> around the yielded content. A layout fingerprint
 * mismatch (e.g. session expired, portal change) falls back to a full load.
 */
(function () {
    'use strict';

    var CACHE_TTL = 10000;   // ms a prefetched page is considered fresh
    var CACHE_MAX = 10;      // max parsed pages kept in memory
    var cache = new Map();   // url (no hash) -> { doc, at }
    var inflight = new Map();
    var current = null;      // AbortController of the active navigation
    var bar = null;

    /* ── Scoped timers & listeners ────────────────────────────────────────
       Scripts inside the swapped container re-execute on every visit. While
       they run we record the setTimeout/setInterval ids and the
       document/window listeners they register, so the NEXT navigation can
       clear them (otherwise auto-refresh timers and handlers would pile up).
       Listeners/timers registered outside a swap (layout scripts, chatbot)
       are never touched. DOMContentLoaded handlers registered by swapped
       scripts fire immediately instead of never. */
    var scopedTimers = [];
    var scopedListeners = [];
    var tracking = false;
    var origST, origSI, origCT, origCI, origDocAdd, origWinAdd;

    function enableScoping() {
        if (origST) return;
        origST = window.setTimeout; origSI = window.setInterval;
        origCT = window.clearTimeout; origCI = window.clearInterval;
        origDocAdd = document.addEventListener;
        origWinAdd = window.addEventListener;

        window.setTimeout = function () {
            var id = origST.apply(window, arguments);
            if (tracking) scopedTimers.push({ id: id, i: false });
            return id;
        };
        window.setInterval = function () {
            var id = origSI.apply(window, arguments);
            if (tracking) scopedTimers.push({ id: id, i: true });
            return id;
        };
        window.clearTimeout = function (id) {
            scopedTimers = scopedTimers.filter(function (t) { return t.id !== id; });
            return origCT(id);
        };
        window.clearInterval = function (id) {
            scopedTimers = scopedTimers.filter(function (t) { return t.id !== id; });
            return origCI(id);
        };
        document.addEventListener = function (type, fn, opts) {
            if (tracking && type === 'DOMContentLoaded') {
                if (document.readyState === 'loading') origDocAdd.call(document, type, fn, opts);
                else origST(function () { fn.call(document); }, 0);
                return;
            }
            if (tracking) scopedListeners.push([document, type, fn, captureOf(opts)]);
            return origDocAdd.call(document, type, fn, opts);
        };
        window.addEventListener = function (type, fn, opts) {
            if (tracking) scopedListeners.push([window, type, fn, captureOf(opts)]);
            return origWinAdd.call(window, type, fn, opts);
        };
    }

    function captureOf(opts) {
        if (typeof opts === 'boolean') return opts;
        return !!(opts && opts.capture);
    }

    function releaseScope() {
        scopedTimers.forEach(function (t) { t.i ? origCI(t.id) : origCT(t.id); });
        scopedTimers = [];
        scopedListeners.forEach(function (l) { l[0].removeEventListener(l[1], l[2], l[3]); });
        scopedListeners = [];
    }

    /* ── Progress bar ── */
    function barStart() {
        if (!bar) {
            bar = document.createElement('div');
            bar.setAttribute('data-pjax-bar', '');
            bar.style.cssText = 'position:fixed;top:0;left:0;height:3px;width:0;' +
                'background:linear-gradient(90deg,#e8b84b,#2a5298);z-index:2147483647;' +
                'transition:width .25s ease,opacity .3s ease;opacity:1;pointer-events:none;';
            document.body.appendChild(bar);
        }
        bar.style.transition = 'width .25s ease';
        bar.style.opacity = '1';
        bar.style.width = '8%';
        origST(function () { if (bar) bar.style.width = '70%'; }, 120);
    }

    function barDone() {
        if (!bar) return;
        bar.style.transition = 'width .2s ease';
        bar.style.width = '100%';
        origST(function () {
            if (!bar) return;
            bar.style.opacity = '0';
            origST(function () {
                if (bar && bar.parentNode) bar.parentNode.removeChild(bar);
                bar = null;
            }, 320);
        }, 180);
    }

    /* ── URL helpers ── */
    var ASSET_RE = /\.(png|jpe?g|gif|svg|webp|ico|css|js|mjs|map|woff2?|ttf|otf|eot|pdf|zip|rar|7z|docx?|xlsx?|pptx?|csv|txt|mp4|webm|mov|mp3|wav|xml|json)$/i;

    function cacheKey(url) {
        var u = new URL(url, location.href);
        u.hash = '';
        return u.href;
    }

    function cacheSet(url, doc) {
        var k = cacheKey(url);
        cache.delete(k);
        cache.set(k, { doc: doc, at: Date.now() });
        if (cache.size > CACHE_MAX) {
            cache.delete(cache.keys().next().value);
        }
    }

    function interceptableLink(a) {
        if (!a || a.target === '_blank' || a.hasAttribute('download')) return false;
        if (a.getAttribute('data-pjax') === '0' || a.hasAttribute('data-no-pjax')) return false;
        var href = a.getAttribute('href');
        if (!href || href.charAt(0) === '#' || /^(mailto|tel|javascript|data|blob):/i.test(href)) return false;
        var url;
        try { url = new URL(a.href, location.href); } catch (e) { return false; }
        if (url.origin !== location.origin) return false;
        if (url.username || url.password) return false;
        if (ASSET_RE.test(url.pathname)) return false;
        return url;
    }

    /* ── Navigation ── */
    function navigate(url, push) {
        var key = cacheKey(url);
        if (current) { current.abort(); current = null; }

        var cached = cache.get(key);
        if (cached && Date.now() - cached.at < CACHE_TTL) {
            applyPage(cached.doc, key, push);
            return;
        }

        barStart();
        var ctrl = new AbortController();
        current = ctrl;
        fetch(key, {
            headers: { 'X-PJAX': '1' },
            signal: ctrl.signal,
            redirect: 'follow',
        }).then(function (r) {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            var ct = r.headers.get('content-type') || '';
            if (ct.indexOf('text/html') === -1) throw new Error('not html');
            var finalUrl = cacheKey(r.url || key);
            return r.text().then(function (t) { return { text: t, url: finalUrl }; });
        }).then(function (res) {
            var doc = new DOMParser().parseFromString(res.text, 'text/html');
            cacheSet(res.url, doc);
            applyPage(doc, res.url, push);
        }).catch(function (err) {
            if (err && err.name === 'AbortError') return;
            hardNavigate(url);
        }).then(function () {
            if (current === ctrl) { current = null; barDone(); }
        });
    }

    function hardNavigate(url) {
        var u = new URL(url, location.href);
        location.assign(u.href);
    }

    /* Fallback to a full load when the fetched page is not the same layout
       (session expired -> login page, portal switch, pjax disabled). */
    function applyPage(doc, url, push) {
        var newLayout = doc.body ? doc.body.getAttribute('data-pjax-layout') : null;
        var curC = document.getElementById('pjax-container');
        var newC = doc.getElementById('pjax-container');
        if (!newLayout || !curC || !newC || newLayout !== document.body.getAttribute('data-pjax-layout')) {
            hardNavigate(url);
            return;
        }

        releaseScope();

        tracking = true;
        try {
            document.title = doc.title || document.title;

            swapHeadStyles(doc);

            curC.innerHTML = newC.innerHTML;
            runScripts(curC);

            copySwap(doc, '[data-pjax="title"]');
            copySwap(doc, '[data-pjax="badge"]');
            swapScriptStack(doc);

            updateSidebarActive(doc);
        } finally {
            tracking = false;
        }

        if (push) {
            try { history.pushState({ pjax: 1 }, '', url); } catch (e) {}
        }

        window.scrollTo(0, 0);
        closeMobileSidebar();
        document.dispatchEvent(new CustomEvent('pjax:navigated', { detail: { url: url } }));
    }

    function copySwap(doc, sel) {
        var cur = document.querySelector(sel);
        var nw = doc.querySelector(sel);
        if (cur && nw) cur.innerHTML = nw.innerHTML;
    }

    /* Per-page styles live between <!--pjax-styles-start--> and
       <!--pjax-styles-end--> comments in <head>. */
    function swapHeadStyles(doc) {
        var curMarks = findMarkers(document.head);
        var newMarks = findMarkers(doc.head);
        if (!curMarks || !newMarks) return;

        var n = curMarks[0].nextSibling;
        while (n && n !== curMarks[1]) {
            var next = n.nextSibling;
            n.parentNode.removeChild(n);
            n = next;
        }
        var m = newMarks[0].nextSibling;
        while (m && m !== newMarks[1]) {
            var next2 = m.nextSibling;
            curMarks[0].parentNode.insertBefore(document.importNode(m, true), curMarks[1]);
            m = next2;
        }
    }

    function findMarkers(head) {
        var start = null, end = null;
        for (var i = 0; i < head.childNodes.length; i++) {
            var node = head.childNodes[i];
            if (node.nodeType === 8) {
                var t = node.textContent.trim();
                if (t === 'pjax-styles-start') start = node;
                else if (t === 'pjax-styles-end') end = node;
            }
        }
        return start && end ? [start, end] : null;
    }

    /* Per-page scripts live in <div id="pjax-scripts">. Fresh script
       elements are created directly from the parsed doc — appending them
       executes them (imported scripts execute too, but creating fresh ones
       keeps external-script ordering and avoids double execution). */
    function swapScriptStack(doc) {
        var curW = document.getElementById('pjax-scripts');
        var newW = doc.getElementById('pjax-scripts');
        if (!curW || !newW) return;
        curW.textContent = '';
        Array.prototype.slice.call(newW.childNodes).forEach(function (node) {
            if (node.nodeName === 'SCRIPT' && isExecutableScript(node)) {
                var s = document.createElement('script');
                for (var i = 0; i < node.attributes.length; i++) {
                    s.setAttribute(node.attributes[i].name, node.attributes[i].value);
                }
                s.text = node.textContent;
                if (s.src) s.async = false;   // keep external scripts ordered
                curW.appendChild(s);
            } else {
                curW.appendChild(document.importNode(node, true));
            }
        });
    }

    function isExecutableScript(old) {
        var type = (old.getAttribute('type') || '').toLowerCase();
        return !type || type === 'text/javascript' || type === 'application/javascript' ||
               type === 'module' || type === 'text/ecmascript';
    }

    function runScripts(scope) {
        Array.prototype.slice.call(scope.querySelectorAll('script')).forEach(function (old) {
            if (!isExecutableScript(old)) return;
            var s = document.createElement('script');
            for (var i = 0; i < old.attributes.length; i++) {
                s.setAttribute(old.attributes[i].name, old.attributes[i].value);
            }
            s.text = old.textContent;
            if (s.src) s.async = false;   // keep external scripts ordered
            old.parentNode.replaceChild(s, old);
        });
    }

    /* The sidebar is part of the persistent shell. Refresh its contents
       (active state, badges) from the fetched page. The <aside> element
       itself stays, so references held by layout scripts remain valid. */
    function updateSidebarActive(doc) {
        var curNav = document.querySelector('aside.sidebar, #sidebar');
        var newNav = doc.querySelector('aside.sidebar, #sidebar');
        if (!curNav || !newNav) return;
        curNav.innerHTML = newNav.innerHTML;
    }

    function closeMobileSidebar() {
        var sb = document.getElementById('sidebar');
        if (sb) sb.classList.remove('open');
        var bd = document.getElementById('sbBackdrop');
        if (bd) bd.classList.remove('open');
        document.body.style.overflow = '';
    }

    /* ── Prefetch on hover / focus / touch ── */
    var lastHover = null;

    function prefetch(a) {
        var url = interceptableLink(a);
        if (!url) return;
        var key = cacheKey(url.href);
        if (key === cacheKey(location.href)) return;
        var cached = cache.get(key);
        if (cached && Date.now() - cached.at < CACHE_TTL) return;
        if (inflight.has(key)) return;

        var ctrl = new AbortController();
        inflight.set(key, ctrl);
        fetch(key, { signal: ctrl.signal, headers: { 'X-PJAX': '1' }, redirect: 'follow' })
            .then(function (r) {
                if (!r.ok) return null;
                var ct = r.headers.get('content-type') || '';
                if (ct.indexOf('text/html') === -1) return null;
                var finalUrl = cacheKey(r.url || key);
                return r.text().then(function (t) {
                    cacheSet(finalUrl, new DOMParser().parseFromString(t, 'text/html'));
                });
            })
            .catch(function () {})
            .then(function () { inflight.delete(key); });
    }

    /* ── Wiring ── */
    function init() {
        if (!document.getElementById('pjax-container')) return;   // not a pjax page
        enableScoping();

        document.addEventListener('click', function (e) {
            if (e.defaultPrevented || e.button !== 0 ||
                e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
            var a = e.target && e.target.closest ? e.target.closest('a') : null;
            var url = interceptableLink(a);
            if (!url) return;
            if (url.pathname === location.pathname && url.search === location.search) {
                if (url.hash && url.hash !== location.hash) {
                    location.hash = url.hash;
                } else {
                    navigate(url.href, false);   // re-fetch current page as a refresh
                }
                return;
            }
            e.preventDefault();
            closeMobileSidebar();
            navigate(url.href, true);
        });

        document.addEventListener('mouseover', function (e) {
            var a = e.target && e.target.closest ? e.target.closest('a') : null;
            if (a && a !== lastHover) {
                lastHover = a;
                prefetch(a);
            }
        });

        document.addEventListener('focusin', function (e) {
            var a = e.target && e.target.closest ? e.target.closest('a') : null;
            if (a) prefetch(a);
        });

        document.addEventListener('touchstart', function (e) {
            var a = e.target && e.target.closest ? e.target.closest('a') : null;
            if (a) prefetch(a);
        }, { passive: true });

        /* Same-origin GET forms (search boxes, filters) also swap instead of
           reloading. POST forms keep their native behaviour. */
        document.addEventListener('submit', function (e) {
            if (e.defaultPrevented) return;
            var form = e.target;
            if (!form || form.tagName !== 'FORM') return;
            if ((form.getAttribute('method') || 'get').toLowerCase() !== 'get') return;
            if (form.hasAttribute('data-no-pjax') || form.getAttribute('target')) return;
            if (form.hasAttribute('data-ajax')) return;
            if (form.querySelector('input[type="file"]')) return;
            if (!document.getElementById('pjax-container')) return;

            var action;
            try { action = new URL(form.getAttribute('action') || location.href, location.href); }
            catch (err) { return; }
            if (action.origin !== location.origin) return;

            e.preventDefault();
            var qs = new URLSearchParams(new FormData(form)).toString();
            navigate(action.pathname + (qs ? '?' + qs : ''), true);
        });

        window.addEventListener('popstate', function () {
            if (!document.getElementById('pjax-container')) return;
            navigate(location.href, false);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
