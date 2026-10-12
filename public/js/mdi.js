/*!
 * ISAMS MDI (no tab strip, no switcher chrome) — DSA portal trial.
 *
 * Keep-alive pages + peek side panel (pin → split view). The topbar has no
 * buttons: the sidebar is the only switcher (clicking a module focuses its
 * open page, or opens it; links/GET forms inside a page navigate that page).
 * There is no close UI — pages stay open until the 10-page limit silently
 * evicts the least recently used one (a toast announces it).
 *
 * Scope: loaded by the student, scholarship, counselor and admin (DSA)
 * layouts — NOT superadmin. All four layouts share the same markup/palette
 * conventions, so the engine is portal-agnostic. pjax.js stays untouched and
 * keeps working for every other portal — this engine pre-empts it in the
 * capture phase (pjax ignores events that are already defaultPrevented) and
 * swallows pjax's popstate registration below.
 *
 * Navigation model:
 *   - sidebar / topbar links → focus the page if open, else if the active
 *     page belongs to the same module navigate it to the module root, else
 *     open a new page
 *   - links / GET forms inside page content → navigate the ACTIVE page
 *   - list rows with a detail link (.mdi-rowlink) → peek preview; the row's
 *     View/Edit buttons still navigate
 *   - links inside the peek/pane open as real pages
 *
 * Revert: remove the <script src="js/mdi.js"> line from the admin layout.
 */
(function () {
    'use strict';

    /* ── 0. Early shims (run at parse time, BEFORE pjax.js initializes) ────
       pjax.js registers its popstate handler at DOMContentLoaded, which fires
       after this file parses. While MDI is not yet active, capture those
       registrations instead of installing them; if MDI init fails we put them
       back so pjax keeps working. Timer/listener wrappers are flag-gated and
       pass everything through outside page-script execution. */
    var mdiReady = false;
    var swallowedPopstate = [];
    var realWinAdd = window.addEventListener;
    window.addEventListener = function (type, fn, opts) {
        if (type === 'popstate' && !mdiReady) { swallowedPopstate.push(fn); return; }
        return realWinAdd.call(window, type, fn, opts);
    };

    var MAX_PAGES = 10;
    var pages = [];            // see makePage
    var activeId = null;       // active page key (pathname+search)
    var activeTok = null;      // active page token
    var tracking = false;      // true while a page's scripts execute
    var docked = false;        // peek content currently pinned as split pane
    var peekCfg = null;
    var tokSeq = 0;

    function makePage(url) {
        var u = new URL(url, location.href);
        return {
            tok: ++tokSeq,                       // stable page identity (LRU, peek guards)
            id: u.pathname + u.search,           // current page key
            prevKey: null,                       // key before an in-page navigation (for history rewind)
            url: u.pathname + u.search,
            pageEl: document.createElement('div'),
            title: 'Loading\u2026', sub: '', docTitle: '', icon: null,
            sidebarHTML: null, stylesHTML: null,
            timers: [], listeners: [], ctrl: null, scrollY: 0
        };
    }

    /* ── Scoped timers & listeners (per page) ─────────────────────────────
       Same contract as pjax.js's scoping, layered UNDER it: page scripts run
       with tracking=true, so timers they set and document/window listeners
       they register are recorded on the page and released when the page is
       closed or reloaded. DOMContentLoaded handlers registered by swapped
       scripts are deferred so they still fire. */
    var origST, origSI, origCT, origCI, origDocAdd, origWinAdd;

    function enableScoping() {
        if (origST) return;
        origST = window.setTimeout; origSI = window.setInterval;
        origCT = window.clearTimeout; origCI = window.clearInterval;
        origDocAdd = document.addEventListener;
        origWinAdd = window.addEventListener;

        window.setTimeout = function () {
            var id = origST.apply(window, arguments);
            if (tracking) curPage().timers.push({ id: id, i: false });
            return id;
        };
        window.setInterval = function () {
            var id = origSI.apply(window, arguments);
            if (tracking) curPage().timers.push({ id: id, i: true });
            return id;
        };
        window.clearTimeout = function (id) {
            pages.forEach(function (p) { p.timers = p.timers.filter(function (t) { return t.id !== id; }); });
            return origCT(id);
        };
        window.clearInterval = function (id) {
            pages.forEach(function (p) { p.timers = p.timers.filter(function (t) { return t.id !== id; }); });
            return origCI(id);
        };
        document.addEventListener = function (type, fn, opts) {
            if (tracking && type === 'DOMContentLoaded') {
                if (document.readyState === 'loading') origDocAdd.call(document, type, fn, opts);
                else origST(function () { fn.call(document); }, 0);
                return;
            }
            if (tracking) curPage().listeners.push([document, type, fn, captureOf(opts)]);
            return origDocAdd.call(document, type, fn, opts);
        };
        window.addEventListener = function (type, fn, opts) {
            if (tracking) curPage().listeners.push([window, type, fn, captureOf(opts)]);
            return origWinAdd.call(window, type, fn, opts);
        };
    }

    function captureOf(opts) {
        if (typeof opts === 'boolean') return opts;
        return !!(opts && opts.capture);
    }

    var loadingPage = null;
    function curPage() { return loadingPage || pages[pages.length - 1] || makePage(location.href); }

    function releaseScope(p) {
        if (p.ctrl) { try { p.ctrl.abort(); } catch (e) {} p.ctrl = null; }
        p.timers.forEach(function (t) { t.i ? origCI(t.id) : origCT(t.id); });
        p.timers = [];
        p.listeners.forEach(function (l) { l[0].removeEventListener(l[1], l[2], l[3]); });
        p.listeners = [];
    }

    /* ── Small helpers ──────────────────────────────────────────────────── */
    var ASSET_RE = /\.(png|jpe?g|gif|svg|webp|ico|css|js|mjs|map|woff2?|ttf|otf|eot|pdf|zip|rar|7z|docx?|xlsx?|pptx?|csv|txt|mp4|webm|mov|mp3|wav|xml|json)$/i;

    function pageKey(url) {
        var u = new URL(url, location.href);
        u.hash = '';
        return u.pathname + u.search;
    }

    function escapeHTML(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function interceptableLink(a) {
        if (!a || a.target === '_blank' || a.hasAttribute('download')) return false;
        if (a.getAttribute('data-pjax') === '0' || a.hasAttribute('data-no-pjax')) return false;
        /* anchors wired to submit a POST form (logout pattern) navigate natively — never fetch them */
        if (/\.\s*submit\s*\(/.test(a.getAttribute('onclick') || '')) return false;
        var href = a.getAttribute('href');
        if (!href || href.charAt(0) === '#' || /^(mailto|tel|javascript|data|blob):/i.test(href)) return false;
        var url;
        try { url = new URL(a.href, location.href); } catch (e) { return false; }
        if (url.origin !== location.origin) return false;
        if (url.username || url.password) return false;
        if (ASSET_RE.test(url.pathname)) return false;
        return url;
    }

    function hardNavigate(url) {
        var u = new URL(url, location.href);
        location.assign(u.href);
    }

    /* Sidebar modules: href → {label, icon, path}. Longest matching prefix
       decides a URL's module (e.g. /admin/students/5 → Students). */
    var MODULES = [];
    function buildModuleIndex() {
        MODULES = [];
        document.querySelectorAll('aside.sidebar a.nav-a').forEach(function (a) {
            var href = a.getAttribute('href');
            if (!href) return;
            var url;
            try { url = new URL(href, location.href); } catch (e) { return; }
            if (url.origin !== location.origin) return;
            var ic = a.querySelector('i');
            var clone = a.cloneNode(true);
            var badge = clone.querySelector('.nav-badge');
            if (badge) badge.remove();
            MODULES.push({
                path: url.pathname.replace(/\/+$/, '') || '/',
                label: (clone.textContent || '').replace(/\s+/g, ' ').trim(),
                icon: ic ? ic.className : 'fas fa-layer-group'
            });
        });
        MODULES.sort(function (x, y) { return y.path.length - x.path.length; });
    }

    function moduleFor(url) {
        var p;
        try { p = new URL(url, location.href).pathname.replace(/\/+$/, '') || '/'; } catch (e) { return null; }
        for (var i = 0; i < MODULES.length; i++) {
            var m = MODULES[i].path;
            if (p === m || p.indexOf(m + '/') === 0) return MODULES[i];
        }
        return null;
    }

    function topPrefix(url) {
        var m = moduleFor(url);
        return m ? m.path : pageKey(url);
    }

    function dashboardUrl() {
        var dash = MODULES.filter(function (m) { return /dashboard/i.test(m.label); })[0];
        return dash ? dash.path : (MODULES.length ? MODULES[MODULES.length - 1].path : '/admin');
    }

    /* ── Progress bar & toast ───────────────────────────────────────────── */
    var barEl = null;
    function barStart() {
        if (!barEl) {
            barEl = document.createElement('div');
            barEl.setAttribute('data-mdi-bar', '');
            barEl.style.cssText = 'position:fixed;top:0;left:262px;right:0;height:3px;z-index:9999;' +
                'background:linear-gradient(90deg,#e8b84b,#2a5298);transform-origin:left;transform:scaleX(0);' +
                'transition:transform .25s linear,opacity .3s;opacity:0;pointer-events:none;';
            document.body.appendChild(barEl);
        }
        barEl.style.opacity = '1';
        barEl.style.transform = 'scaleX(0.25)';
        origST(function () { if (barEl) barEl.style.transform = 'scaleX(0.65)'; }, 120);
    }
    function barDone() {
        if (!barEl) return;
        barEl.style.transform = 'scaleX(1)';
        origST(function () {
            if (!barEl) return;
            barEl.style.opacity = '0';
            origST(function () {
                if (barEl && barEl.parentNode) barEl.parentNode.removeChild(barEl);
                barEl = null;
            }, 320);
        }, 180);
    }

    function toast(msg) {
        var el = document.createElement('div');
        el.setAttribute('data-mdi-toast', '');
        el.className = 'mdi-toast';
        el.innerHTML = '<i class="fas fa-info-circle"></i> ' + escapeHTML(msg);
        document.body.appendChild(el);
        requestAnimationFrame(function () { el.classList.add('mdi-show'); });
        origST(function () {
            el.classList.remove('mdi-show');
            origST(function () { el.remove(); }, 320);
        }, 3600);
    }

    /* ── Injected UI (styles + peek/pane) ───────────────────────────────── */
    var peekEl, peekBody, paneEl, paneBody, pagesWrap;

    var CSS = [
        '.mdi-page{display:none;min-width:0;animation:mdiIn .28s ease both;}',
        '.mdi-page.active{display:block;}',
        '@keyframes mdiIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}',
        '#pjax-container.mdi-split{display:flex;align-items:stretch;}',
        '#pjax-container.mdi-split .mdi-page{flex:1;}',
        '#mdiPane{display:none;flex-direction:column;width:440px;min-width:320px;flex-shrink:0;border:1px solid var(--bd);background:var(--card);border-radius:var(--r);box-shadow:var(--shadow);margin:0 0 10px 18px;align-self:flex-start;}',
        '#pjax-container.mdi-split #mdiPane{display:flex;position:sticky;top:86px;max-height:calc(100vh - 108px);overflow-y:auto;}',
        '.mdi-peek-head{padding:13px 16px;border-bottom:1px solid var(--bd);display:flex;align-items:center;gap:9px;background:var(--card);flex-shrink:0;position:sticky;top:0;z-index:2;}',
        '.mdi-peek-head h3{font-family:Sora,sans-serif;font-size:14px;font-weight:700;color:var(--g);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin:0;}',
        '.mdi-peek-head .mdi-sub{font-size:11px;color:var(--tm);}',
        '.mdi-peek-body{flex:1;overflow-y:auto;padding:16px;}',
        '.mdi-iconbtn{width:31px;height:31px;border-radius:var(--rs);border:1.5px solid var(--bd);background:var(--bg);color:var(--tm);display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:12px;flex-shrink:0;transition:all .2s;}',
        '.mdi-iconbtn:hover{background:var(--g);color:#fff;border-color:var(--g);}',
        '.mdi-iconbtn.pin:hover{background:var(--y);color:#0d3318;border-color:var(--y);}',
        '.mdi-peek{position:fixed;top:0;right:0;bottom:0;width:460px;max-width:100vw;background:var(--card);border-left:1px solid var(--bd);box-shadow:-18px 0 50px rgba(13,51,24,.28);z-index:940;transform:translateX(106%);transition:transform .3s cubic-bezier(.2,.7,.3,1);display:flex;flex-direction:column;}',
        '.mdi-peek.open{transform:none;}',
        '@media(max-width:900px){.mdi-peek{width:100vw;}#pjax-container.mdi-split #mdiPane{width:100%;max-height:none;position:static;}[data-mdi-bar]{left:0!important;}}',
        '.mdi-toast{position:fixed;bottom:64px;right:24px;background:var(--sb,#0d3318);color:#fff;padding:12px 18px;border-radius:var(--rs);font-size:13px;box-shadow:0 10px 30px rgba(0,0,0,.25);opacity:0;transform:translateY(14px);transition:all .3s;z-index:9998;display:flex;gap:9px;align-items:center;max-width:380px;pointer-events:none;}',
        '.mdi-toast.mdi-show{opacity:1;transform:translateY(0);}',
        '.mdi-toast i{color:var(--y);}',
        '.mdi-skel .sk-line{height:13px;border-radius:6px;background:linear-gradient(90deg,#e2e9f2 25%,#f4f8f4 50%,#e2e9f2 75%);background-size:200% 100%;animation:mdiShim 1.1s infinite;margin-bottom:13px;}',
        '.mdi-skel .sk-line.h{height:24px;width:38%;}',
        '@keyframes mdiShim{to{background-position:-200% 0}}',
        'tr.mdi-rowlink,.inbox-row.mdi-rowlink{cursor:pointer;}'
    ].join('\n');

    function injectStyles() {
        var st = document.createElement('style');
        st.setAttribute('data-mdi-styles', '');
        st.textContent = CSS;
        document.head.appendChild(st);
    }

    function injectPeek() {
        peekEl = document.createElement('div');
        peekEl.className = 'mdi-peek';
        peekEl.innerHTML =
            '<div class="mdi-peek-head">' +
              '<div style="flex:1;min-width:0;"><h3 class="mdi-peek-title">—</h3><div class="mdi-sub mdi-peek-sub"></div></div>' +
              '<button type="button" class="mdi-iconbtn pin" data-act="pin" title="Pin — keep docked beside the list (split view)"><i class="fas fa-thumbtack"></i></button>' +
              '<button type="button" class="mdi-iconbtn" data-act="close" title="Close (Esc)"><i class="fas fa-times"></i></button>' +
            '</div>' +
            '<div class="mdi-peek-body"></div>';
        document.body.appendChild(peekEl);
        peekBody = peekEl.querySelector('.mdi-peek-body');

        paneEl = document.createElement('div');
        paneEl.id = 'mdiPane';
        paneEl.innerHTML =
            '<div class="mdi-peek-head">' +
              '<div style="flex:1;min-width:0;"><h3 class="mdi-pane-title">—</h3><div class="mdi-sub mdi-pane-sub"></div></div>' +
              '<button type="button" class="mdi-iconbtn" data-act="unpin" title="Unpin — back to peek overlay"><i class="fas fa-window-restore"></i></button>' +
              '<button type="button" class="mdi-iconbtn" data-act="close" title="Close pane"><i class="fas fa-times"></i></button>' +
            '</div>' +
            '<div class="mdi-peek-body"></div>';
        paneBody = paneEl.querySelector('.mdi-peek-body');

        peekEl.addEventListener('click', function (e) {
            var b = e.target.closest('[data-act]');
            if (!b) return;
            if (b.getAttribute('data-act') === 'pin') pinPeek();
            else closePeek();
        });
        paneEl.addEventListener('click', function (e) {
            var b = e.target.closest('[data-act]');
            if (!b) return;
            if (b.getAttribute('data-act') === 'unpin') unpinPeek();
            else closePane();
        });
    }

    /* ── Page ops ───────────────────────────────────────────────────────── */
    function pageByTok(tok) { return pages.find(function (p) { return p.tok === Number(tok); }); }
    function page(id) { return pages.find(function (p) { return p.id === id; }); }

    function skeletonHTML() {
        return '<div class="mdi-skel" style="padding:4px 2px;"><div class="sk-line h"></div>' +
            '<div class="sk-line" style="width:70%"></div><div class="sk-line" style="width:90%"></div>' +
            '<div class="sk-line" style="width:55%"></div><div class="sk-line" style="width:80%"></div></div>';
    }

    /* The currently-rendered document becomes the first MDI page — its
       scripts already ran on the full load, so nothing is re-executed. */
    function adoptInitialPage() {
        var container = document.getElementById('pjax-container');
        var p = makePage(location.href);
        p.pageEl.className = 'mdi-page active';
        while (container.firstChild) p.pageEl.appendChild(container.firstChild);
        container.appendChild(p.pageEl);
        container.appendChild(paneEl);

        var t = document.querySelector('[data-pjax="title"]');
        p.title = t ? ((t.querySelector('.tp-title') || t).textContent.trim() || 'Page') : 'Page';
        p.sub = t && t.querySelector('.tp-sub') ? t.querySelector('.tp-sub').textContent : '';
        p.docTitle = document.title;
        p.sidebarHTML = (document.querySelector('aside.sidebar') || {}).innerHTML || '';
        p.stylesHTML = currentHeadStyles();
        var m = moduleFor(p.url);
        p.icon = m ? m.icon : 'fas fa-layer-group';

        pagesWrap = container;
        pages.push(p);
        wirePeeks(p.pageEl);
        activeTok = p.tok;
        activeId = p.id;
    }

    /* Head styles live between <!--pjax-styles-start--> and --end--> markers;
       each page keeps its own slice and re-applies it on activation. */
    function headMarkers(root) {
        var start = null, end = null;
        for (var i = 0; i < root.childNodes.length; i++) {
            var n = root.childNodes[i];
            if (n.nodeType === 8) {
                var t = n.textContent.trim();
                if (t === 'pjax-styles-start') start = n;
                else if (t === 'pjax-styles-end') end = n;
            }
        }
        return start && end ? [start, end] : null;
    }

    function currentHeadStyles() {
        var mk = headMarkers(document.head);
        if (!mk) return '';
        var out = '', n = mk[0].nextSibling;
        while (n && n !== mk[1]) {
            out += n.nodeType === 8 ? '<!--' + n.textContent + '-->' : (n.outerHTML || '');
            n = n.nextSibling;
        }
        return out;
    }

    function headStylesOf(doc) {
        var mk = headMarkers(doc.head);
        if (!mk) return '';
        var out = '', n = mk[0].nextSibling;
        while (n && n !== mk[1]) {
            out += n.nodeType === 8 ? '<!--' + n.textContent + '-->' : (n.outerHTML || '');
            n = n.nextSibling;
        }
        return out;
    }

    function applyHeadStyles(html) {
        var mk = headMarkers(document.head);
        if (!mk) return;
        var n = mk[0].nextSibling;
        while (n && n !== mk[1]) {
            var next = n.nextSibling;
            n.parentNode.removeChild(n);
            n = next;
        }
        var tpl = document.createElement('template');
        tpl.innerHTML = html;
        while (tpl.content.firstChild) mk[0].parentNode.insertBefore(tpl.content.firstChild, mk[1]);
    }

    /* Re-key a page after an in-page navigation (list → detail, filter…). */
    function rekeyPage(p, key, url) {
        if (p.id === key) return;
        p.prevKey = p.id;
        p.id = key;
        p.url = url;
        if (activeTok === p.tok) activeId = key;
    }

    function openPage(url, opts) {
        opts = opts || {};
        var key = pageKey(url);

        var existing = page(key);
        if (existing) {
            if (opts.force) reloadPage(existing);
            else activatePage(existing.tok, { push: opts.push !== false });
            return;
        }

        /* history rewind: back/forward into a key this page just left */
        var rewound = pages.filter(function (p) { return p.prevKey === key; })[0];
        if (rewound) {
            activatePage(rewound.tok, { push: false, skipScroll: true });
            loadIntoPage(rewound, url, {});
            return;
        }

        if (pages.length >= MAX_PAGES) {
            var victim = pages[0];
            closePage(victim.tok, true);
            toast('Page limit reached — closed oldest page \u201C' + victim.title + '\u201D');
        }

        var mod = moduleFor(url);
        var p = makePage(url);
        p.icon = mod ? mod.icon : 'fas fa-layer-group';
        p.pageEl.className = 'mdi-page';
        p.pageEl.innerHTML = skeletonHTML();
        pagesWrap.insertBefore(p.pageEl, pagesWrap.firstChild);
        pages.push(p);
        activatePage(p.tok, { push: opts.push !== false, skipScroll: true });
        loadIntoPage(p, p.url, opts);
    }

    function loadIntoPage(p, url, opts) {
        opts = opts || {};
        if (p.ctrl) { try { p.ctrl.abort(); } catch (e) {} }
        var ctrl = new AbortController();
        p.ctrl = ctrl;
        loadingPage = p;
        barStart();
        fetch(url, { headers: { 'X-PJAX': '1' }, signal: ctrl.signal, redirect: 'follow' })
            .then(function (r) {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                var ct = r.headers.get('content-type') || '';
                if (ct.indexOf('text/html') === -1) throw new Error('not html');
                return r.text();
            })
            .then(function (text) {
                if (ctrl.signal.aborted) return;
                var doc = new DOMParser().parseFromString(text, 'text/html');
                var layout = doc.body ? doc.body.getAttribute('data-pjax-layout') : null;
                var newC = doc.getElementById('pjax-container');
                if (!layout || layout !== document.body.getAttribute('data-pjax-layout') || !newC) {
                    hardNavigate(url);            // session expired / portal switch
                    return;
                }

                releaseScope(p);                  // clears timers/listeners of the previous load
                p.ctrl = null;
                rekeyPage(p, pageKey(url), pageKey(url));

                tracking = true;
                try {
                    p.pageEl.innerHTML = newC.innerHTML;
                    runScriptsIn(p.pageEl);
                    execScriptStack(doc);

                    var t = doc.querySelector('[data-pjax="title"]');
                    p.title = t ? ((t.querySelector('.tp-title') || t).textContent.trim() || 'Page') : (doc.title || 'Page');
                    p.sub = t && t.querySelector('.tp-sub') ? t.querySelector('.tp-sub').textContent : '';
                    p.docTitle = doc.title || p.docTitle;
                    var sb = doc.querySelector('aside.sidebar');
                    p.sidebarHTML = sb ? sb.innerHTML : p.sidebarHTML;
                    p.stylesHTML = headStylesOf(doc) || p.stylesHTML;
                    var m = moduleFor(p.url);
                    if (m) p.icon = m.icon;
                } finally {
                    tracking = false;
                    loadingPage = null;
                }

                wirePeeks(p.pageEl);
                applyHeadStyles(p.stylesHTML);
                document.title = p.docTitle;
                if (p.tok === activeTok) applyChrome(p);
                document.dispatchEvent(new CustomEvent('pjax:navigated', { detail: { url: p.url } }));
                barDone();
            })
            .catch(function (err) {
                loadingPage = null;
                barDone();
                if (err && err.name === 'AbortError') return;
                toast('Could not load the page — falling back to a full load.');
                hardNavigate(url);
            });
    }

    function reloadPage(p) {
        releaseScope(p);
        p.pageEl.innerHTML = skeletonHTML();
        if (p.tok === activeTok) applyChrome(p);
        loadIntoPage(p, p.url, {});
    }

    /* Re-apply per-page chrome: title block, sidebar state, head styles. */
    function applyChrome(p) {
        var t = document.querySelector('[data-pjax="title"]');
        if (t) {
            var tt = t.querySelector('.tp-title'), ts = t.querySelector('.tp-sub');
            if (tt) tt.textContent = p.title; else t.textContent = p.title;
            if (ts) ts.textContent = p.sub;
        }
        if (p.sidebarHTML) {
            var sb = document.querySelector('aside.sidebar');
            if (sb && sb.innerHTML !== p.sidebarHTML) sb.innerHTML = p.sidebarHTML;
        }
        applyHeadStyles(p.stylesHTML);
        if (p.docTitle) document.title = p.docTitle;
        if (window.innerWidth <= 900) {
            var s = document.getElementById('sidebar');
            if (s) s.classList.remove('open');
            var bd = document.getElementById('sbBackdrop');
            if (bd) bd.classList.remove('open');
        }
    }

    function activatePage(tok, opts) {
        opts = opts || {};
        var p = pageByTok(tok);
        if (!p) return;
        if (activeTok && activeTok !== p.tok && pageByTok(activeTok)) pageByTok(activeTok).scrollY = window.scrollY;
        activeTok = p.tok;
        activeId = p.id;

        pages.splice(pages.indexOf(p), 1);
        pages.push(p);                                   // LRU: most recent last

        pages.forEach(function (q) {
            q.pageEl.classList.toggle('active', q.tok === p.tok);
        });
        pagesWrap.insertBefore(p.pageEl, pagesWrap.firstChild);   // active page wins duplicate-ID lookups

        applyChrome(p);
        if (opts.push !== false) {
            try { history.pushState({ mdi: 1 }, '', p.url); } catch (e) {}
        }
        window.scrollTo(0, opts.skipScroll ? 0 : (p.scrollY || 0));
        /* late-expanding content (lazy images) can clamp the first restore —
           re-apply once shortly after */
        if (!opts.skipScroll && p.scrollY) {
            origST(function () {
                if (activeTok === p.tok && Math.abs(window.scrollY - p.scrollY) > 2) window.scrollTo(0, p.scrollY);
            }, 350);
        }
    }

    function closePage(tok, silent) {
        var p = pageByTok(tok);
        if (!p) return;
        if (pages.length === 1) {                        // never leave the shell empty
            hardNavigate(dashboardUrl());
            return;
        }
        releaseScope(p);
        p.pageEl.remove();
        pages.splice(pages.indexOf(p), 1);

        if (activeTok === p.tok) {
            activeTok = null;
            activatePage(pages[pages.length - 1].tok, { push: true });
        }
    }

    /* ── Script execution (scoped while tracking=true) ──────────────────── */
    function isExecutableScript(old) {
        var type = (old.getAttribute('type') || '').toLowerCase();
        return !type || type === 'text/javascript' || type === 'application/javascript' ||
               type === 'module' || type === 'text/ecmascript';
    }

    function runScriptsIn(scope) {
        Array.prototype.slice.call(scope.querySelectorAll('script')).forEach(function (old) {
            if (!isExecutableScript(old)) return;
            var s = document.createElement('script');
            for (var i = 0; i < old.attributes.length; i++) {
                s.setAttribute(old.attributes[i].name, old.attributes[i].value);
            }
            s.text = old.textContent;
            if (s.src) s.async = false;
            old.parentNode.replaceChild(s, old);
        });
    }

    function execScriptStack(doc) {
        var curW = document.getElementById('pjax-scripts');
        var newW = doc.getElementById('pjax-scripts');
        if (!curW) return;
        curW.textContent = '';
        if (!newW) return;
        Array.prototype.slice.call(newW.childNodes).forEach(function (node) {
            if (node.nodeName === 'SCRIPT' && isExecutableScript(node)) {
                var s = document.createElement('script');
                for (var i = 0; i < node.attributes.length; i++) {
                    s.setAttribute(node.attributes[i].name, node.attributes[i].value);
                }
                s.text = node.textContent;
                if (s.src) s.async = false;
                curW.appendChild(s);                     // append executes; elements stay for external loads
            }
        });
    }

    /* ── Peek rows: list rows that preview a detail page ──────────────────
       A "detail" URL is any same-origin path whose LAST segment is a numeric
       id with at least two path segments before it — /admin/students/61,
       /scholarship/scholars/9, /student/announcements/4 … — so the rule is
       portal-agnostic. Sub-pages that don't end in a bare number (…/edit,
       …/view, …/pdf, …/create) are excluded. */
    function isDetailPath(pathname) {
        var seg = pathname.replace(/\/+$/, '').split('/').filter(Boolean);
        return seg.length >= 3 && /^\d+$/.test(seg[seg.length - 1]);
    }

    function detailUrlOf(row) {
        var links = row.querySelectorAll('a[href]');
        for (var i = 0; i < links.length; i++) {
            var h = links[i].getAttribute('href');
            if (!h || h.charAt(0) === '#') continue;
            try {
                var u = new URL(h, location.href);
                if (u.origin === location.origin && isDetailPath(u.pathname)) return u.pathname + u.search;
            } catch (e) {}
        }
        var oc = row.getAttribute('onclick') || '';
        var m = oc.match(/window\.location\s*=\s*['"]([^'"]+)['"]/);
        if (m) {
            try {
                var u2 = new URL(m[1], location.href);
                if (u2.origin === location.origin && isDetailPath(u2.pathname)) return u2.pathname + u2.search;
            } catch (e) {}
        }
        return null;
    }

    function wirePeeks(scope) {
        Array.prototype.slice.call(scope.querySelectorAll('tbody tr, .inbox-row')).forEach(function (row) {
            if (row.closest('.mdi-peek, #mdiPane')) return;
            if (row.hasAttribute('data-mdi-peek')) return;
            var url = detailUrlOf(row);
            if (url) {
                row.setAttribute('data-mdi-peek', url);
                row.classList.add('mdi-rowlink');
                row.setAttribute('title', 'Click to peek — pin it to keep it beside the list');
            }
        });
    }

    /* ── Peek panel + split view ────────────────────────────────────────── */
    function peekTarget() {
        return docked
            ? { body: paneBody, t: paneEl.querySelector('.mdi-pane-title'), s: paneEl.querySelector('.mdi-pane-sub') }
            : { body: peekBody, t: peekEl.querySelector('.mdi-peek-title'), s: peekEl.querySelector('.mdi-peek-sub') };
    }

    function openPeek(url) {
        peekCfg = { url: url };
        var t = peekTarget();
        t.t.textContent = 'Loading\u2026';
        t.s.textContent = '';
        t.body.innerHTML = '<div class="mdi-skel"><div class="sk-line h"></div><div class="sk-line" style="width:75%"></div>' +
            '<div class="sk-line" style="width:90%"></div><div class="sk-line" style="width:60%"></div></div>';
        if (!docked) peekEl.classList.add('open');
        fetch(url, { headers: { 'X-PJAX': '1' }, redirect: 'follow' })
            .then(function (r) {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.text();
            })
            .then(function (text) {
                if (!peekCfg || peekCfg.url !== url) return;   // superseded by another peek
                var doc = new DOMParser().parseFromString(text, 'text/html');
                var layout = doc.body ? doc.body.getAttribute('data-pjax-layout') : null;
                var newC = doc.getElementById('pjax-container');
                if (layout !== document.body.getAttribute('data-pjax-layout') || !newC) {
                    closePeek();
                    openPage(url);                             // not a portal page — open fully instead
                    return;
                }
                var t2 = peekTarget();
                var pt = doc.querySelector('[data-pjax="title"]');
                t2.t.textContent = pt && pt.querySelector('.tp-title') ? pt.querySelector('.tp-title').textContent.trim() : (doc.title || 'Preview');
                t2.s.textContent = pt && pt.querySelector('.tp-sub') ? pt.querySelector('.tp-sub').textContent.trim() : '';
                var clone = newC.cloneNode(true);
                clone.querySelectorAll('script').forEach(function (s) { s.remove(); });
                clone.querySelectorAll('input,select,textarea,button,[onclick]').forEach(function (el) {
                    el.setAttribute('disabled', 'disabled');
                });
                t2.body.innerHTML = '';
                while (clone.firstChild) t2.body.appendChild(clone.firstChild);
                t2.body.scrollTop = 0;
            })
            .catch(function () {
                if (peekCfg && peekCfg.url === url) {
                    closePeek();
                    toast('Preview unavailable — opening the page instead.');
                    openPage(url);
                }
            });
    }

    function closePeek() {
        peekEl.classList.remove('open');
        peekCfg = null;
    }

    function pinPeek() {
        if (!peekCfg || docked) return;
        while (peekBody.firstChild) paneBody.appendChild(peekBody.firstChild);
        paneEl.querySelector('.mdi-pane-title').textContent = peekEl.querySelector('.mdi-peek-title').textContent;
        paneEl.querySelector('.mdi-pane-sub').textContent = peekEl.querySelector('.mdi-peek-sub').textContent;
        docked = true;
        pagesWrap.classList.add('mdi-split');
        peekEl.classList.remove('open');
        toast('Pinned — the list and the detail now sit side by side.');
    }

    function unpinPeek() {
        if (!docked) return;
        while (paneBody.firstChild) peekBody.appendChild(paneBody.firstChild);
        docked = false;
        pagesWrap.classList.remove('mdi-split');
        if (peekCfg) {
            peekEl.querySelector('.mdi-peek-title').textContent = paneEl.querySelector('.mdi-pane-title').textContent;
            peekEl.querySelector('.mdi-peek-sub').textContent = paneEl.querySelector('.mdi-pane-sub').textContent;
            peekEl.classList.add('open');
        }
    }

    function closePane() {
        docked = false;
        pagesWrap.classList.remove('mdi-split');
        paneBody.innerHTML = '';
        peekCfg = null;
    }

    /* ── Interception (capture phase — runs before pjax's bubble handlers) ── */
    function closeOverlaysAfterNav() {
        if (peekEl.classList.contains('open')) closePeek();
    }

    function navigateActivePage(p, url) {
        var key = pageKey(url);
        if (key === p.id) { reloadPage(p); return; }
        loadIntoPage(p, url, {});
        rekeyPage(p, key, key);
        try { history.pushState({ mdi: 1 }, '', key); } catch (err) {}
    }

    function wire() {
        document.addEventListener('click', function (e) {
            if (!mdiReady || e.defaultPrevented || e.button !== 0 ||
                e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

            /* peek rows (also hijacks .inbox-row inline window.location navs) */
            var row = e.target.closest('.mdi-rowlink');
            if (row && row.hasAttribute('data-mdi-peek') &&
                !e.target.closest('a,button,input,select,textarea,label,form,[data-no-peek]')) {
                e.preventDefault();
                e.stopPropagation();
                openPeek(row.getAttribute('data-mdi-peek'));
                return;
            }

            var a = e.target.closest('a');
            if (!a) return;
            var url = interceptableLink(a);
            if (!url) return;

            var key = pageKey(url.href);
            var inOverlays = !!e.target.closest('.mdi-peek, #mdiPane');

            if (e.target.closest('aside.sidebar, .topbar')) {
                /* module entry points: focus the page if one is open; else if
                   the active page belongs to the same module, navigate it back
                   to the module root; else open a new page. */
                e.preventDefault();
                e.stopPropagation();
                var existing = page(key);
                if (existing) { activatePage(existing.tok, { push: true }); return; }
                var act = pageByTok(activeTok);
                if (act && !inOverlays && topPrefix(act.url) === topPrefix(url.href)) {
                    navigateActivePage(act, url.href);
                    return;
                }
                openPage(url.href, {});
                return;
            }

            /* links inside the peek/pane open as real pages */
            if (inOverlays) {
                e.preventDefault();
                e.stopPropagation();
                openPage(url.href, {});
                return;
            }

            /* content links navigate the active page in place */
            e.preventDefault();
            e.stopPropagation();
            closeOverlaysAfterNav();
            var actP = pageByTok(activeTok);
            if (actP) navigateActivePage(actP, url.href);
            else openPage(url.href, {});
        }, true);

        /* Same-origin GET forms load into the active page; POST forms stay native. */
        document.addEventListener('submit', function (e) {
            if (!mdiReady || e.defaultPrevented) return;
            var form = e.target;
            if (!form || form.tagName !== 'FORM') return;
            if ((form.getAttribute('method') || 'get').toLowerCase() !== 'get') return;
            if (form.hasAttribute('data-no-pjax') || form.hasAttribute('data-ajax') || form.getAttribute('target')) return;
            if (form.querySelector('input[type="file"]')) return;

            var action;
            try { action = new URL(form.getAttribute('action') || location.href, location.href); }
            catch (err) { return; }
            if (action.origin !== location.origin) return;

            e.preventDefault();
            e.stopPropagation();
            var qs = new URLSearchParams(new FormData(form)).toString();
            var target = action.pathname + (qs ? '?' + qs : '');
            if (e.target.closest('.mdi-peek, #mdiPane')) { openPage(target, {}); return; }
            closeOverlaysAfterNav();
            var actP = pageByTok(activeTok);
            if (actP) navigateActivePage(actP, target);
            else openPage(target, {});
        }, true);

        realWinAdd.call(window, 'popstate', function () {
            if (!mdiReady) return;
            openPage(location.pathname + location.search, { push: false });
        });

        document.addEventListener('click', function (e) {   // outside-click dismisses the peek
            if (peekEl.classList.contains('open') &&
                !e.target.closest('.mdi-peek, #mdiPane, .mdi-rowlink')) {
                closePeek();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && peekEl.classList.contains('open')) closePeek();
        });
    }

    /* ── Init ───────────────────────────────────────────────────────────── */
    function init() {
        try {
            if (!document.getElementById('pjax-container')) return;     // not a portal page
            if (!document.body.getAttribute('data-pjax-layout')) return;
            enableScoping();
            injectStyles();
            injectPeek();
            buildModuleIndex();
            adoptInitialPage();
            wire();
            mdiReady = true;
            try {
                if (!sessionStorage.getItem('mdi-hint')) {
                    sessionStorage.setItem('mdi-hint', '1');
                    toast('MDI: pages stay open in the background — use the sidebar to switch, click a list row to peek.');
                }
            } catch (e) {}
        } catch (err) {
            /* give control back to plain pjax.js */
            try { console.warn('[mdi] init failed, falling back to pjax:', err); } catch (e2) {}
            swallowedPopstate.splice(0).forEach(function (fn) { realWinAdd.call(window, 'popstate', fn); });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
