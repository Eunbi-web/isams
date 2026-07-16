/**
 * ISAMS Global AJAX System
 * Handles all form submissions and data operations without full page reloads
 */
(function () {
    'use strict';

    // ── Toast Notification ─────────────────────────────────────────────────
    function toast(msg, type) {
        type = type || 'success';
        var colors = {
            success: { bg: '#d0f0d8', border: '#1a7a4a', color: '#0d4a1e', icon: 'fas fa-check-circle' },
            error:   { bg: '#fde8e6', border: '#c0392b', color: '#7a1a14', icon: 'fas fa-exclamation-circle' },
            info:    { bg: '#e8f5ec', border: '#2d9e4f', color: '#1a6b2f', icon: 'fas fa-info-circle' },
            warning: { bg: '#fef3cd', border: '#d68910', color: '#6b4a00', icon: 'fas fa-exclamation-triangle' },
        };
        var s = colors[type] || colors.success;

        var el = document.createElement('div');
        el.style.cssText = [
            'position:fixed', 'top:20px', 'right:20px', 'z-index:9999',
            'background:' + s.bg, 'border:1.5px solid ' + s.border,
            'color:' + s.color, 'padding:12px 18px', 'border-radius:10px',
            'font-size:13px', 'font-weight:600', 'max-width:360px',
            'box-shadow:0 6px 24px rgba(0,0,0,.15)',
            'display:flex', 'align-items:center', 'gap:10px',
            'animation:isamsToastIn .3s ease',
            'font-family:DM Sans,sans-serif',
        ].join(';');
        el.innerHTML = '<i class="' + s.icon + '" style="font-size:16px;flex-shrink:0;"></i><span>' + msg + '</span>';

        var style = document.getElementById('isams-toast-style');
        if (!style) {
            style = document.createElement('style');
            style.id = 'isams-toast-style';
            style.textContent = '@keyframes isamsToastIn{from{opacity:0;transform:translateX(20px)}to{opacity:1;transform:translateX(0)}}';
            document.head.appendChild(style);
        }

        document.body.appendChild(el);
        setTimeout(function () {
            el.style.transition = 'opacity .4s,transform .4s';
            el.style.opacity = '0';
            el.style.transform = 'translateX(20px)';
            setTimeout(function () { el.remove(); }, 400);
        }, 3500);
    }

    // ── Spinner ────────────────────────────────────────────────────────────
    function spinner(btn, on) {
        if (!btn) return;
        if (on) {
            btn.dataset.origHtml = btn.innerHTML;
            btn.innerHTML = '<span style="display:inline-flex;align-items:center;gap:7px;"><span style="width:14px;height:14px;border:2.5px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:isamsToastIn .6s linear infinite;display:inline-block;"></span>Please wait...</span>';
            btn.disabled = true;
        } else {
            if (btn.dataset.origHtml) btn.innerHTML = btn.dataset.origHtml;
            btn.disabled = false;
        }
    }

    // ── Loading overlay for a card/table ──────────────────────────────────
    function tableLoader(container, on) {
        if (!container) return;
        var overlay = container.querySelector('.isams-loader');
        if (on) {
            if (overlay) return;
            overlay = document.createElement('div');
            overlay.className = 'isams-loader';
            overlay.style.cssText = 'position:absolute;inset:0;background:rgba(255,255,255,.75);display:flex;align-items:center;justify-content:center;border-radius:inherit;z-index:10;backdrop-filter:blur(2px);';
            overlay.innerHTML = '<div style="display:flex;flex-direction:column;align-items:center;gap:10px;"><div style="width:32px;height:32px;border:3px solid #cde0d0;border-top-color:#1a6b2f;border-radius:50%;animation:isamsSpinA .7s linear infinite;"></div><div style="font-size:12px;color:#5a7a60;font-weight:600;">Loading...</div></div>';
            var spinStyle = document.getElementById('isams-spin-style');
            if (!spinStyle) {
                spinStyle = document.createElement('style');
                spinStyle.id = 'isams-spin-style';
                spinStyle.textContent = '@keyframes isamsSpinA{to{transform:rotate(360deg)}}';
                document.head.appendChild(spinStyle);
            }
            container.style.position = 'relative';
            container.appendChild(overlay);
        } else {
            if (overlay) overlay.remove();
        }
    }

    // ── CSRF Token ─────────────────────────────────────────────────────────
    function csrf() {
        var m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.getAttribute('content') : '';
    }

    // ── AJAX POST helper ───────────────────────────────────────────────────
    function ajaxPost(url, data, onSuccess, onError) {
        var body;
        if (data instanceof FormData) {
            data.append('_token', csrf());
            body = data;
        } else {
            data._token = csrf();
            body = JSON.stringify(data);
        }

        var headers = { 'X-CSRF-TOKEN': csrf(), 'X-Requested-With': 'XMLHttpRequest' };
        if (!(data instanceof FormData)) headers['Content-Type'] = 'application/json';

        fetch(url, { method: 'POST', headers: headers, body: body })
            .then(function (r) {
                return r.json().then(function (d) { return { ok: r.ok, status: r.status, data: d }; });
            })
            .then(function (r) {
                if (r.ok && r.data) { onSuccess(r.data); }
                else { onError(r.data && r.data.message ? r.data.message : 'Something went wrong. Please try again.'); }
            })
            .catch(function (e) { onError('Network error: ' + e.message); });
    }

    // ── AJAX GET helper ────────────────────────────────────────────────────
    function ajaxGet(url, onSuccess, onError) {
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(onSuccess)
            .catch(function (e) { if (onError) onError('Network error: ' + e.message); });
    }

    // ── Intercept AJAX-flagged forms ───────────────────────────────────────
    // Add data-ajax="true" to any form to make it AJAX-submitted
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (form.dataset.ajax !== 'true') return;
        e.preventDefault();

        var btn = form.querySelector('[type=submit]');
        var card = form.closest('.card') || form.closest('[data-ajax-container]');
        spinner(btn, true);
        if (card) tableLoader(card, true);

        var fd = new FormData(form);
        // Handle _method override
        var method = (fd.get('_method') || form.method || 'POST').toUpperCase();

        var headers = { 'X-CSRF-TOKEN': csrf(), 'X-Requested-With': 'XMLHttpRequest' };
        if (method !== 'POST') { fd.append('_method', method); }

        fetch(form.action, { method: 'POST', headers: headers, body: fd })
            .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
            .then(function (r) {
                spinner(btn, false);
                if (card) tableLoader(card, false);
                if (r.ok && r.data) {
                    toast(r.data.message || 'Done!', 'success');
                    if (r.data.redirect) { window.location.href = r.data.redirect; return; }
                    if (r.data.reload) { window.location.reload(); return; }
                    if (r.data.html && card) {
                        var target = card.querySelector('[data-ajax-target]');
                        if (target) target.innerHTML = r.data.html;
                    }
                    // Close modal if inside one
                    var modal = form.closest('.mo');
                    if (modal) modal.classList.remove('open');
                    form.reset();
                } else {
                    toast((r.data && r.data.message) || 'Something went wrong.', 'error');
                }
            })
            .catch(function (e) {
                spinner(btn, false);
                if (card) tableLoader(card, false);
                toast('Network error: ' + e.message, 'error');
            });
    });

    // ── Intercept AJAX-flagged approve/reject buttons ──────────────────────
    // Add data-ajax-action="approve|reject" data-id="appId" to buttons
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-ajax-action]');
        if (!btn) return;
        e.preventDefault();

        var action = btn.dataset.ajaxAction;
        var id     = btn.dataset.id;
        var url    = btn.dataset.url;
        var confirmMsg = btn.dataset.confirm;

        if (confirmMsg && !confirm(confirmMsg)) return;

        var row  = btn.closest('tr');
        var card = btn.closest('.card');

        spinner(btn, true);
        if (card) tableLoader(card, true);

        ajaxPost(url || '/admin/applications/' + id + '/' + action, {}, function (data) {
            spinner(btn, false);
            if (card) tableLoader(card, false);
            toast(data.message || 'Done!', 'success');

            // Update status badge in row without reloading
            if (row) {
                var statusCell = row.querySelector('[data-status-cell]');
                if (statusCell && data.status) {
                    var cls = data.status === 'Approved' ? 'b-s' : data.status === 'Rejected' ? 'b-d' : 'b-w';
                    statusCell.innerHTML = '<span class="badge ' + cls + '">' + data.status + '</span>';
                }
                // Hide action buttons after action
                var actionsCell = row.querySelector('[data-actions-cell]');
                if (actionsCell && (action === 'approve' || action === 'reject')) {
                    var pendingBtns = actionsCell.querySelectorAll('[data-ajax-action="approve"],[data-ajax-action="reject"]');
                    pendingBtns.forEach(function (b) { b.style.display = 'none'; });
                }
            }
            if (data.reload) setTimeout(function () { window.location.reload(); }, 800);
        }, function (err) {
            spinner(btn, false);
            if (card) tableLoader(card, false);
            toast(err, 'error');
        });
    });

    // ── Live search on tables ──────────────────────────────────────────────
    // Add data-ajax-search="true" data-ajax-url="/url" to a search input
    // Add data-ajax-table="true" to the target table tbody
    document.querySelectorAll('[data-ajax-search]').forEach(function (input) {
        var delay;
        input.addEventListener('input', function () {
            clearTimeout(delay);
            var val = input.value;
            var url = input.dataset.ajaxUrl;
            var tbodyId = input.dataset.ajaxTable;
            var tbody = tbodyId ? document.getElementById(tbodyId) : null;
            if (!url || !tbody) return;

            delay = setTimeout(function () {
                tableLoader(tbody.closest('.card'), true);
                ajaxGet(url + '?search=' + encodeURIComponent(val), function (data) {
                    tableLoader(tbody.closest('.card'), false);
                    if (data.html) tbody.innerHTML = data.html;
                }, function (err) {
                    tableLoader(tbody.closest('.card'), false);
                    toast(err, 'error');
                });
            }, 400);
        });
    });

    // ── Filter dropdowns (auto-submit with AJAX) ───────────────────────────
    // Add data-ajax-filter="true" data-ajax-form="formId" to selects
    document.querySelectorAll('[data-ajax-filter]').forEach(function (sel) {
        sel.addEventListener('change', function () {
            var formId = sel.dataset.ajaxForm;
            var form = formId ? document.getElementById(formId) : sel.closest('form');
            if (!form) return;
            var card = form.closest('.card');
            if (card) tableLoader(card, true);
            // Standard form submit (page reload for filters is OK, but faster)
            form.submit();
        });
    });

    // ── Delete confirmation with AJAX ──────────────────────────────────────
    // Add data-ajax-delete="true" data-url="/url" to delete buttons
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-ajax-delete]');
        if (!btn) return;
        e.preventDefault();
        var msg = btn.dataset.confirm || 'Are you sure you want to delete this?';
        if (!confirm(msg)) return;

        var url = btn.dataset.url;
        var row = btn.closest('tr');
        var card = btn.closest('.card');

        spinner(btn, true);
        fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf(), 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json' },
            body: JSON.stringify({ _method: 'DELETE', _token: csrf() })
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            toast(data.message || 'Deleted successfully.', 'success');
            if (row) {
                row.style.transition = 'opacity .3s';
                row.style.opacity = '0';
                setTimeout(function () { row.remove(); }, 300);
            } else if (data.reload) {
                window.location.reload();
            }
        })
        .catch(function (e) {
            spinner(btn, false);
            toast('Network error: ' + e.message, 'error');
        });
    });

    // ── Modal helpers ──────────────────────────────────────────────────────
    window.isamsOpenModal  = function (id) { var m = document.getElementById(id); if (m) m.classList.add('open'); };
    window.isamsCloseModal = function (id) { var m = document.getElementById(id); if (m) m.classList.remove('open'); };
    window.isamsToast      = toast;
    window.isamsAjaxPost   = ajaxPost;
    window.isamsAjaxGet    = ajaxGet;

    // Show existing session flash messages as toasts on load
    window.addEventListener('DOMContentLoaded', function () {
        var flashes = document.querySelectorAll('.alert.al-s, .alert.al-d, .alert.al-w, .alert.al-i');
        flashes.forEach(function (el) {
            var type = el.classList.contains('al-s') ? 'success'
                     : el.classList.contains('al-d') ? 'error'
                     : el.classList.contains('al-w') ? 'warning' : 'info';
            var text = el.textContent.trim();
            if (text) {
                toast(text, type);
                el.style.display = 'none'; // hide inline alert, show as toast instead
            }
        });
    });

    console.log('ISAMS AJAX System loaded');
})();
