
<?php
    $isaacFirstName = explode(' ', trim(auth()->user()->name ?? 'User'))[0];
?>
<div id="isamsChatToggle" class="isams-chat-toggle" title="Chat with ISAAC" aria-label="Open ISAAC chat">
    <i class="fas fa-robot" id="isamsChatToggleIcon"></i>
    <span class="isams-chat-badge" id="isamsChatBadge" style="display:none;">0</span>
</div>

<div id="isamsChatPanel" class="isams-chat-panel" style="display:none;" role="dialog" aria-label="ISAAC AI Assistant chat">
    <div class="isams-chat-header">
        <div class="isams-chat-avatar"><i class="fas fa-robot"></i></div>
        <div class="isams-chat-header-info">
            <div class="isams-chat-header-name">ISAAC</div>
            <div class="isams-chat-header-sub"><span class="isams-chat-dot"></span>ISAMS AI Assistant</div>
        </div>
        <button type="button" class="isams-chat-minimize" id="isamsChatMinimize" aria-label="Minimize chat"><i class="fas fa-minus"></i></button>
    </div>

    <div class="isams-chat-messages" id="isamsChatMessages"></div>

    <div class="isams-chat-chips" id="isamsChatChips">
        <button type="button" class="isams-chat-chip">What can you do</button>
        <button type="button" class="isams-chat-chip">How do I apply for a scholarship</button>
        <button type="button" class="isams-chat-chip">How is my AI score calculated</button>
        <button type="button" class="isams-chat-chip">What is the Scholarship Passport</button>
        <button type="button" class="isams-chat-chip">How to request counseling</button>
        <button type="button" class="isams-chat-chip">What are the eligibility levels</button>
        <button type="button" class="isams-chat-chip">How does PH Sync work</button>
        <button type="button" class="isams-chat-chip">What is RBAC in ISAMS</button>
        <button type="button" class="isams-chat-chip">What portals does ISAMS have</button>
        <button type="button" class="isams-chat-chip">How do notifications work</button>
    </div>

    <div class="isams-chat-input-area">
        <input type="text" id="isamsChatInput" class="isams-chat-input" placeholder="Ask me anything about ISAMS..." maxlength="500" autocomplete="off">
        <button type="button" id="isamsChatSend" class="isams-chat-send" aria-label="Send message"><i class="fas fa-paper-plane"></i></button>
    </div>
</div>

<style>
/* ===== ISAAC chat widget (scoped: isams-chat-) ===== */
.isams-chat-toggle {
    position: fixed; bottom: 28px; right: 28px; z-index: 9999;
    width: 56px; height: 56px; border-radius: 50%;
    background: linear-gradient(135deg, var(--g), var(--gm));
    color: #fff; border: none; cursor: pointer;
    box-shadow: 0 4px 20px rgba(0,0,0,0.25);
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; transition: transform 0.3s ease, opacity 0.3s ease;
}
.isams-chat-toggle:hover { transform: scale(1.06); }
.isams-chat-toggle.isams-chat-open i { display: inline-block; transition: transform 0.5s ease; transform: rotate(360deg); }
.isams-chat-toggle.isams-chat-unread { animation: isams-chat-pulse 2s infinite; }
@keyframes isams-chat-pulse {
    0% { box-shadow: 0 4px 20px rgba(0,0,0,0.25), 0 0 0 0 rgba(240,192,32,0.5); }
    70% { box-shadow: 0 4px 20px rgba(0,0,0,0.25), 0 0 0 12px rgba(240,192,32,0); }
    100% { box-shadow: 0 4px 20px rgba(0,0,0,0.25), 0 0 0 0 rgba(240,192,32,0); }
}
.isams-chat-badge {
    position: absolute; top: -4px; right: -4px;
    background: var(--y); color: #0d3318;
    min-width: 20px; height: 20px; padding: 0 5px;
    border-radius: 10px; font-size: 11px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    font-family: 'DM Sans', sans-serif; line-height: 1;
}
.isams-chat-panel {
    position: fixed; bottom: 96px; right: 28px; z-index: 9998;
    width: 360px; max-height: 520px;
    background: var(--card); border: 1.5px solid var(--bd); border-radius: 16px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.18);
    display: none; flex-direction: column; overflow: hidden;
    font-family: 'DM Sans', sans-serif;
    animation: isams-chat-slideup 0.25s ease-out;
}
.isams-chat-panel.isams-chat-visible { display: flex; }
@keyframes isams-chat-slideup {
    from { opacity: 0; transform: translateY(16px); }
    to { opacity: 1; transform: translateY(0); }
}
.isams-chat-header {
    background: linear-gradient(135deg, #0d3318, #1a6b2f);
    height: 64px; padding: 12px 16px;
    display: flex; align-items: center; gap: 10px; flex-shrink: 0;
}
.isams-chat-avatar {
    width: 36px; height: 36px; border-radius: 50%;
    background: rgba(255,255,255,0.15);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.isams-chat-avatar i { color: var(--y); font-size: 16px; }
.isams-chat-header-info { flex: 1; min-width: 0; }
.isams-chat-header-name { color: #fff; font-weight: 700; font-size: 14px; font-family: 'Sora', sans-serif; }
.isams-chat-header-sub { color: rgba(255,255,255,0.7); font-size: 11px; display: flex; align-items: center; gap: 5px; }
.isams-chat-dot {
    width: 6px; height: 6px; border-radius: 50%;
    background: #2d9e4f; display: inline-block;
    animation: isams-chat-dotpulse 2s infinite;
}
@keyframes isams-chat-dotpulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}
.isams-chat-minimize {
    background: none; border: none; cursor: pointer;
    color: rgba(255,255,255,0.7); font-size: 14px; padding: 6px;
}
.isams-chat-minimize:hover { color: #fff; }
.isams-chat-messages {
    overflow-y: auto; max-height: 280px; padding: 12px;
    display: flex; flex-direction: column; gap: 8px;
    background: var(--bg);
}
.isams-chat-row { display: flex; gap: 6px; align-items: flex-end; }
.isams-chat-row.isams-chat-user-row { justify-content: flex-end; }
.isams-chat-bot-avatar {
    width: 24px; height: 24px; border-radius: 50%; flex-shrink: 0;
    background: var(--gp); color: var(--gm);
    display: flex; align-items: center; justify-content: center;
    font-size: 11px;
}
.isams-chat-bubble {
    max-width: 85%; padding: 8px 12px; font-size: 13px; line-height: 1.5;
    word-wrap: break-word;
}
.isams-chat-bubble.isams-chat-bot {
    background: var(--gp); color: var(--tx);
    border-radius: 4px 12px 12px 12px;
}
.isams-chat-bubble.isams-chat-user {
    background: linear-gradient(135deg, var(--g), var(--gm));
    color: #fff; border-radius: 12px 4px 12px 12px;
}
.isams-chat-bubble p { margin: 0 0 4px 0; }
.isams-chat-bubble p:last-child { margin-bottom: 0; }
.isams-chat-time { font-size: 10px; color: var(--tm); margin-top: 2px; padding: 0 4px; }
.isams-chat-row.isams-chat-user-row .isams-chat-time { text-align: right; }
.isams-chat-typing { display: flex; gap: 4px; padding: 4px 2px; }
.isams-chat-typing span {
    width: 7px; height: 7px; border-radius: 50%; background: var(--gm);
    animation: isams-chat-bounce 1.2s infinite;
}
.isams-chat-typing span:nth-child(2) { animation-delay: 0.2s; }
.isams-chat-typing span:nth-child(3) { animation-delay: 0.4s; }
@keyframes isams-chat-bounce {
    0%, 60%, 100% { transform: translateY(0); opacity: 0.5; }
    30% { transform: translateY(-5px); opacity: 1; }
}
.isams-chat-chips {
    display: flex; gap: 6px; padding: 8px 12px;
    overflow-x: auto; scrollbar-width: none;
    border-top: 1px solid var(--bd); background: var(--card); flex-shrink: 0;
}
.isams-chat-chips::-webkit-scrollbar { display: none; }
.isams-chat-chip {
    background: var(--gp); border: 1px solid var(--bd); border-radius: 20px;
    padding: 4px 12px; font-size: 11px; color: var(--g);
    cursor: pointer; white-space: nowrap; font-family: Calibri, sans-serif;
    transition: background 0.2s, color 0.2s;
}
.isams-chat-chip:hover { background: var(--gm); color: #fff; }
.isams-chat-input-area {
    padding: 10px 12px; display: flex; gap: 8px; align-items: center;
    border-top: 1px solid var(--bd); background: var(--card);
    border-radius: 0 0 16px 16px; flex-shrink: 0;
}
.isams-chat-input {
    flex: 1; padding: 8px 12px; border: 1.5px solid var(--bd);
    border-radius: var(--rs); font-size: 13px;
    background: var(--bg); color: var(--tx); font-family: 'DM Sans', sans-serif;
    outline: none; min-width: 0;
}
.isams-chat-input:focus { border-color: var(--gm); box-shadow: 0 0 0 3px rgba(45,158,79,0.1); }
.isams-chat-input:disabled { opacity: 0.6; }
.isams-chat-send {
    width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0;
    background: linear-gradient(135deg, var(--g), var(--gm));
    color: #fff; border: none; cursor: pointer; font-size: 13px;
    display: flex; align-items: center; justify-content: center;
}
.isams-chat-send:disabled { opacity: 0.5; cursor: not-allowed; }
@media (max-width: 900px) {
    .isams-chat-toggle { bottom: 20px; right: 16px; }
    .isams-chat-panel { right: 16px; }
}
@media (max-width: 480px) {
    .isams-chat-panel { width: calc(100vw - 32px); right: 16px; }
}
</style>

<script>
(function () {
    var ISAAC_FIRST_NAME = <?php echo json_encode($isaacFirstName, 15, 512) ?>;
    var ISAAC_ASK_URL = <?php echo json_encode(route('chatbot.ask'), 15, 512) ?>;
    var CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

    var toggleBtn = document.getElementById('isamsChatToggle');
    var toggleIcon = document.getElementById('isamsChatToggleIcon');
    var badge = document.getElementById('isamsChatBadge');
    var panel = document.getElementById('isamsChatPanel');
    var minimizeBtn = document.getElementById('isamsChatMinimize');
    var messagesEl = document.getElementById('isamsChatMessages');
    var chipsEl = document.getElementById('isamsChatChips');
    var inputEl = document.getElementById('isamsChatInput');
    var sendBtn = document.getElementById('isamsChatSend');

    var isOpen = false;
    var waiting = false;
    var welcomed = false;
    var unreadCount = 0;
    var conversationHistory = [];

    function nowTime() {
        var d = new Date();
        var h = d.getHours() % 12 || 12;
        var m = ('0' + d.getMinutes()).slice(-2);
        return h + ':' + m + (d.getHours() >= 12 ? ' PM' : ' AM');
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function inlineFormat(escaped) {
        return escaped.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
    }

    function formatReply(text) {
        var lines = text.split('\n');
        var html = '';
        var buffer = [];
        var isListLine = function (l) { return /^\s*(\d+\.\s|[-*\u2022]\s)/.test(l); };
        lines.forEach(function (line) {
            var trimmed = line.trim();
            if (trimmed === '') return;
            if (isListLine(trimmed)) {
                if (buffer.length) { html += '<p>' + inlineFormat(buffer.join(' ')) + '</p>'; buffer = []; }
                html += '<p>' + inlineFormat(escapeHtml(trimmed)) + '</p>';
            } else {
                buffer.push(escapeHtml(trimmed));
            }
        });
        if (buffer.length) html += '<p>' + inlineFormat(buffer.join(' ')) + '</p>';
        return html || '<p>' + inlineFormat(escapeHtml(text)) + '</p>';
    }

    function scrollToBottom() { messagesEl.scrollTop = messagesEl.scrollHeight; }

    function appendUserMessage(text) {
        var row = document.createElement('div');
        row.className = 'isams-chat-row isams-chat-user-row';
        row.innerHTML = '<div><div class="isams-chat-bubble isams-chat-user"></div><div class="isams-chat-time">' + nowTime() + '</div></div>';
        row.querySelector('.isams-chat-bubble').textContent = text;
        messagesEl.appendChild(row);
        scrollToBottom();
    }

    function appendBotMessage(text) {
        var row = document.createElement('div');
        row.className = 'isams-chat-row';
        row.innerHTML = '<div class="isams-chat-bot-avatar"><i class="fas fa-robot"></i></div>' +
            '<div><div class="isams-chat-bubble isams-chat-bot">' + formatReply(text) + '</div><div class="isams-chat-time">' + nowTime() + '</div></div>';
        messagesEl.appendChild(row);
        scrollToBottom();
    }

    function showTyping() {
        var row = document.createElement('div');
        row.className = 'isams-chat-row';
        row.id = 'isamsChatTypingRow';
        row.innerHTML = '<div class="isams-chat-bot-avatar"><i class="fas fa-robot"></i></div>' +
            '<div class="isams-chat-bubble isams-chat-bot"><div class="isams-chat-typing"><span></span><span></span><span></span></div></div>';
        messagesEl.appendChild(row);
        scrollToBottom();
    }

    function hideTyping() {
        var row = document.getElementById('isamsChatTypingRow');
        if (row) row.remove();
    }

    function updateBadge() {
        if (unreadCount > 0 && !isOpen) {
            badge.style.display = 'flex';
            badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
            toggleBtn.classList.add('isams-chat-unread');
        } else {
            badge.style.display = 'none';
            toggleBtn.classList.remove('isams-chat-unread');
        }
    }

    function setWaiting(state) {
        waiting = state;
        inputEl.disabled = state;
        sendBtn.disabled = state;
        inputEl.style.opacity = state ? '0.6' : '1';
        sendBtn.style.opacity = state ? '0.5' : '1';
    }

    function sendMessage(presetText) {
        var text = (typeof presetText === 'string' ? presetText : inputEl.value).trim();
        if (!text || waiting) return;
        appendUserMessage(text);
        inputEl.value = '';
        setWaiting(true);
        showTyping();

        fetch(ISAAC_ASK_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                message: text,
                history: conversationHistory.slice(-8)
            })
        })
        .then(function (r) {
            if (!r.ok) throw new Error('bad response');
            return r.json();
        })
        .then(function (data) {
            hideTyping();
            var reply = (data && data.reply) ? data.reply : 'Sorry, I could not connect to ISAAC right now. Please try again.';
            appendBotMessage(reply);
            conversationHistory.push({ role: 'user', content: text });
            conversationHistory.push({ role: 'assistant', content: reply });
            if (conversationHistory.length > 10) conversationHistory = conversationHistory.slice(-10);
            setWaiting(false);
            if (!isOpen) { unreadCount++; updateBadge(); }
        })
        .catch(function () {
            hideTyping();
            appendBotMessage('Sorry, I could not connect to ISAAC right now. Please try again.');
            setWaiting(false);
        });
    }

    function showWelcome() {
        if (welcomed) return;
        welcomed = true;
        appendBotMessage('Hi ' + ISAAC_FIRST_NAME + '! I am ISAAC, your ISAMS AI Assistant. I know everything about this system — scholarships, applications, AI eligibility, counseling, notifications, and more. How can I help you today?');
    }

    function toggleChat() {
        isOpen = !isOpen;
        if (isOpen) {
            panel.classList.add('isams-chat-visible');
            panel.style.display = 'flex';
            toggleBtn.classList.add('isams-chat-open');
            toggleIcon.classList.remove('fa-robot');
            toggleIcon.classList.add('fa-times');
            unreadCount = 0;
            updateBadge();
            showWelcome();
            inputEl.focus();
        } else {
            panel.classList.remove('isams-chat-visible');
            panel.style.display = 'none';
            toggleBtn.classList.remove('isams-chat-open');
            toggleIcon.classList.remove('fa-times');
            toggleIcon.classList.add('fa-robot');
        }
    }

    toggleBtn.addEventListener('click', toggleChat);
    minimizeBtn.addEventListener('click', toggleChat);
    sendBtn.addEventListener('click', function () { sendMessage(); });
    inputEl.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    chipsEl.addEventListener('click', function (e) {
        var chip = e.target.closest('.isams-chat-chip');
        if (!chip) return;
        if (!isOpen) toggleChat();
        sendMessage(chip.textContent.trim());
    });
})();
</script>
<?php /**PATH C:\Users\Acer\Herd\isams\resources\views/chatbot/chatbot-widget.blade.php ENDPATH**/ ?>