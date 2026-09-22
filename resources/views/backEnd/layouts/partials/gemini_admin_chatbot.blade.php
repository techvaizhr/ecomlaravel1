@php
    $geminiWidgetHistory = session('gemini_admin_chat_history', []);
@endphp

<style>
#gemini-admin-widget {
    --gaw-primary: #6366f1;
    --gaw-secondary: #a855f7;
    --gaw-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%);
    --gaw-dark: #090d16;
    font-family: inherit;
}
#gemini-admin-widget * { box-sizing: border-box; }

/* FLOATING TRIGGER BUTTON */
#gaw-toggle {
    position: fixed;
    right: 24px;
    bottom: 84px;
    z-index: 99990;
    width: 58px;
    height: 58px;
    border-radius: 50%;
    border: none;
    cursor: pointer;
    background: var(--gaw-gradient);
    color: #fff;
    box-shadow: 0 10px 28px rgba(139, 92, 246, 0.45);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
#gaw-toggle:hover {
    transform: scale(1.08) translateY(-2px);
    box-shadow: 0 14px 36px rgba(139, 92, 246, 0.6);
}
#gaw-toggle.open {
    background: #1e293b;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.4);
}

/* SIDE CHAT DRAWER PANEL */
#gaw-panel {
    position: fixed;
    top: 0;
    right: 0;
    width: 420px;
    max-width: 100vw;
    height: 100vh;
    z-index: 99989;
    background: #ffffff;
    box-shadow: -10px 0 50px rgba(15, 23, 42, 0.18);
    display: flex;
    flex-direction: column;
    transform: translateX(100%);
    transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}
#gaw-panel.open { transform: translateX(0); }

/* PANEL HEADER */
#gaw-header {
    background: linear-gradient(135deg, #090d16 0%, #1e1b4b 100%);
    color: #fff;
    padding: 18px 20px;
    flex-shrink: 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}
#gaw-header h5 {
    margin: 0;
    font-size: 15.5px;
    font-weight: 700;
    color: #ffffff !important;
    display: flex;
    align-items: center;
    gap: 8px;
}
#gaw-header p {
    margin: 4px 0 0;
    font-size: 11.5px;
    color: #94a3b8 !important;
}
.gaw-status-dot {
    width: 8px;
    height: 8px;
    background: #22c55e;
    border-radius: 50%;
    display: inline-block;
    box-shadow: 0 0 8px #22c55e;
}
#gaw-header-actions {
    display: flex;
    gap: 6px;
    margin-top: 12px;
    flex-wrap: wrap;
}
#gaw-header-actions button, #gaw-header-actions a {
    font-size: 11.5px;
    padding: 5px 12px;
    border-radius: 20px;
    border: 1px solid rgba(255,255,255,0.18);
    background: rgba(255,255,255,0.08);
    color: #f1f5f9;
    cursor: pointer;
    text-decoration: none;
    line-height: 1.3;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.2s ease;
}
#gaw-header-actions button:hover, #gaw-header-actions a:hover {
    background: rgba(255,255,255,0.2);
    border-color: rgba(255,255,255,0.3);
    color: #fff;
}

/* MESSAGES LIST */
#gaw-messages {
    flex: 1;
    overflow-y: auto;
    padding: 18px;
    background: #f8fafc;
    scroll-behavior: smooth;
}
#gaw-messages .gaw-msg {
    display: flex;
    gap: 10px;
    margin-bottom: 16px;
    animation: gawSlide 0.2s ease-out;
}
@keyframes gawSlide {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}
#gaw-messages .gaw-msg.user { flex-direction: row-reverse; }

#gaw-messages .gaw-avatar {
    width: 32px;
    height: 32px;
    border-radius: 10px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    color: #fff;
}
#gaw-messages .gaw-msg.user .gaw-avatar {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
}
#gaw-messages .gaw-msg.bot .gaw-avatar {
    background: var(--gaw-gradient);
}

#gaw-messages .gaw-bubble {
    max-width: 82%;
    padding: 11px 14px;
    border-radius: 15px;
    font-size: 13px;
    line-height: 1.55;
    white-space: pre-wrap;
    word-break: break-word;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
}
#gaw-messages .gaw-msg.user .gaw-bubble {
    background: linear-gradient(135deg, #4f46e5, #6366f1);
    color: #fff;
    border-bottom-right-radius: 3px;
}
#gaw-messages .gaw-msg.bot .gaw-bubble {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    color: #1e293b;
    border-bottom-left-radius: 3px;
}

/* EMPTY STATE */
#gaw-empty {
    text-align: center;
    padding: 26px 10px;
    color: #64748b;
}
.gaw-empty-icon {
    width: 50px;
    height: 50px;
    border-radius: 15px;
    background: #eef2ff;
    color: var(--gaw-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin: 0 auto 12px;
}
#gaw-empty strong {
    color: #1e293b;
    font-size: 14.5px;
    display: block;
    margin-bottom: 4px;
}
#gaw-empty p {
    font-size: 12px;
    color: #64748b;
    margin-bottom: 16px;
}
#gaw-suggestions {
    display: flex;
    flex-direction: column;
    gap: 8px;
    text-align: left;
}
#gaw-suggestions button {
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #334155;
    border-radius: 12px;
    padding: 9px 12px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}
#gaw-suggestions button:hover {
    border-color: #818cf8;
    background: #f5f7ff;
    color: var(--gaw-primary);
    transform: translateX(3px);
}
#gaw-suggestions button i {
    color: var(--gaw-primary);
    font-size: 13px;
}

/* TYPING INDICATOR */
#gaw-typing {
    display: none;
    padding: 6px 18px 10px;
    font-size: 12px;
    color: var(--gaw-primary);
    font-weight: 600;
    background: #f8fafc;
    align-items: center;
    gap: 6px;
}
#gaw-typing.show { display: flex; }

/* INPUT SECTION */
#gaw-input-wrap {
    padding: 12px 14px;
    border-top: 1px solid #e2e8f0;
    background: #ffffff;
    flex-shrink: 0;
}
#gaw-input-form {
    display: flex;
    align-items: flex-end;
    gap: 8px;
    background: #f8fafc;
    border: 1.5px solid #cbd5e1;
    border-radius: 14px;
    padding: 6px 8px 6px 12px;
    transition: all 0.2s ease;
}
#gaw-input-form:focus-within {
    background: #ffffff;
    border-color: var(--gaw-primary);
    box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
}
#gaw-input {
    flex: 1;
    resize: none;
    border: none;
    background: transparent;
    font-size: 13px;
    max-height: 90px;
    min-height: 28px;
    outline: none;
    color: #1e293b;
    padding: 4px 0;
    line-height: 1.4;
}
#gaw-send {
    background: var(--gaw-gradient);
    border: none;
    color: #fff;
    border-radius: 10px;
    padding: 6px 14px;
    font-weight: 700;
    font-size: 12.5px;
    cursor: pointer;
    flex-shrink: 0;
    height: 32px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all 0.2s ease;
}
#gaw-send:hover:not(:disabled) {
    transform: scale(1.03);
    box-shadow: 0 4px 12px rgba(168, 85, 247, 0.35);
}
#gaw-send:disabled {
    opacity: 0.55;
    cursor: not-allowed;
}

/* OVERLAY */
#gaw-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.4);
    z-index: 99988;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
    backdrop-filter: blur(2px);
}
#gaw-overlay.show {
    opacity: 1;
    pointer-events: auto;
}

@media (max-width: 480px) {
    #gaw-panel { width: 100vw; }
    #gaw-toggle { right: 16px; bottom: 80px; width: 52px; height: 52px; }
}
</style>

<div id="gemini-admin-widget" aria-hidden="true">
    <div id="gaw-overlay"></div>

    <aside id="gaw-panel" role="dialog" aria-label="Gemini AI Assistant">
        <div id="gaw-header">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h5><i class="fas fa-robot"></i> Gemini Assistant <span class="gaw-status-dot"></span></h5>
                    <p>স্টোর, সেলস ও সেটিংস সম্পর্কে যেকোনো প্রশ্ন করুন</p>
                </div>
                <button type="button" id="gaw-close-x" style="background:rgba(255,255,255,0.1);border:none;color:#fff;font-size:14px;cursor:pointer;border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;transition:background 0.2s;" aria-label="Close">✕</button>
            </div>
            <div id="gaw-header-actions">
                <button type="button" id="gaw-refresh"><i class="fas fa-sync-alt"></i> রিফ্রেশ</button>
                <button type="button" id="gaw-clear"><i class="fas fa-trash-alt"></i> মুছুন</button>
                <a href="{{ route('admin.gemini_chat.index') }}" target="_blank"><i class="fas fa-external-link-alt"></i> ফুল পেজ</a>
            </div>
        </div>

        <div id="gaw-messages"></div>
        <div id="gaw-typing"><i class="fas fa-spinner fa-spin"></i> উত্তর ভাবছে...</div>

        <div id="gaw-input-wrap">
            <form id="gaw-input-form">
                <textarea id="gaw-input" rows="1" placeholder="প্রশ্ন টাইপ করুন..." maxlength="4000"></textarea>
                <button type="submit" id="gaw-send"><i class="fas fa-paper-plane"></i></button>
            </form>
        </div>
    </aside>

    <button type="button" id="gaw-toggle" title="Gemini Assistant" aria-label="Open Gemini Assistant">
        <i class="fas fa-sparkles"></i>
    </button>
</div>

<script>
(function () {
    var history = @json($geminiWidgetHistory);
    var routes = {
        send: @json(route('admin.gemini_chat.send')),
        clear: @json(route('admin.gemini_chat.clear')),
        refresh: @json(route('admin.gemini_chat.refresh_context')),
        csrf: @json(csrf_token())
    };

    var panel = document.getElementById('gaw-panel');
    var overlay = document.getElementById('gaw-overlay');
    var toggle = document.getElementById('gaw-toggle');
    var messages = document.getElementById('gaw-messages');
    var typing = document.getElementById('gaw-typing');
    var input = document.getElementById('gaw-input');
    var form = document.getElementById('gaw-input-form');
    var sendBtn = document.getElementById('gaw-send');
    var isOpen = false;

    function escapeHtml(t) {
        var d = document.createElement('div');
        d.textContent = t;
        return d.innerHTML;
    }

    function renderHistory() {
        messages.innerHTML = '';
        if (!history.length) {
            messages.innerHTML =
                '<div id="gaw-empty">' +
                '<div class="gaw-empty-icon"><i class="fas fa-robot"></i></div>' +
                '<strong>কী জানতে চান?</strong>' +
                '<p>অর্ডার, প্রোডাক্ট, ফ্রড চেক বা সেটিংস — যেকোনো কিছু জিজ্ঞাসা করুন।</p>' +
                '<div id="gaw-suggestions">' +
                '<button type="button" data-q="আজ কতটি নতুন অর্ডার এসেছে?"><i class="fas fa-shopping-bag"></i> আজকের মোট অর্ডার কত?</button>' +
                '<button type="button" data-q="Pending product কিভাবে approve করব?"><i class="fas fa-check-circle"></i> প্রোডাক্ট অ্যাপ্রুভ করার নিয়ম</button>' +
                '<button type="button" data-q="Fraud check কিভাবে করব?"><i class="fas fa-shield-alt"></i> কাস্টমার ফ্রড চেক কীভাবে করে?</button>' +
                '</div></div>';
            bindSuggestions();
            return;
        }
        history.forEach(function (m) {
            appendBubble(m.role === 'user' ? 'user' : 'bot', m.text, false);
        });
        scrollBottom();
    }

    function bindSuggestions() {
        messages.querySelectorAll('[data-q]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                sendMessage(btn.getAttribute('data-q'));
            });
        });
    }

    function appendBubble(role, text, scroll) {
        var empty = document.getElementById('gaw-empty');
        if (empty) empty.remove();

        var isUser = role === 'user';
        var div = document.createElement('div');
        div.className = 'gaw-msg ' + (isUser ? 'user' : 'bot');
        div.innerHTML =
            '<div class="gaw-avatar"><i class="fas fa-' + (isUser ? 'user' : 'robot') + '"></i></div>' +
            '<div class="gaw-bubble">' + escapeHtml(text) + '</div>';
        messages.appendChild(div);
        if (scroll !== false) scrollBottom();
    }

    function scrollBottom() {
        messages.scrollTop = messages.scrollHeight;
    }

    function openPanel() {
        isOpen = true;
        panel.classList.add('open');
        overlay.classList.add('show');
        toggle.classList.add('open');
        toggle.innerHTML = '<i class="fas fa-times"></i>';
        document.getElementById('gemini-admin-widget').setAttribute('aria-hidden', 'false');
        setTimeout(function () { input.focus(); }, 300);
    }

    function closePanel() {
        isOpen = false;
        panel.classList.remove('open');
        overlay.classList.remove('show');
        toggle.classList.remove('open');
        toggle.innerHTML = '<i class="fas fa-sparkles"></i>';
        document.getElementById('gemini-admin-widget').setAttribute('aria-hidden', 'true');
    }

    function sendMessage(text) {
        text = (text || '').trim();
        if (!text) return;

        appendBubble('user', text);
        history.push({ role: 'user', text: text });
        input.value = '';
        input.style.height = 'auto';
        sendBtn.disabled = true;
        typing.classList.add('show');

        fetch(routes.send, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': routes.csrf,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message: text })
        })
        .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
        .then(function (res) {
            if (res.ok && res.data.success) {
                appendBubble('bot', res.data.reply);
                history.push({ role: 'model', text: res.data.reply });
            } else {
                var err = (res.data && res.data.message) ? res.data.message : 'ব্যর্থ হয়েছে';
                appendBubble('bot', '⚠️ ' + err);
                history.pop();
            }
        })
        .catch(function () {
            appendBubble('bot', '⚠️ সংযোগ ব্যর্থ। আবার চেষ্টা করুন।');
            history.pop();
        })
        .finally(function () {
            sendBtn.disabled = false;
            typing.classList.remove('show');
        });
    }

    toggle.addEventListener('click', function () {
        isOpen ? closePanel() : openPanel();
    });
    document.getElementById('gaw-close-x').addEventListener('click', closePanel);
    overlay.addEventListener('click', closePanel);

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        sendMessage(input.value);
    });

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form.dispatchEvent(new Event('submit'));
        }
    });

    input.addEventListener('input', function () {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 90) + 'px';
    });

    document.getElementById('gaw-clear').addEventListener('click', function () {
        if (!confirm('চ্যাট মুছবেন?')) return;
        fetch(routes.clear, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': routes.csrf, 'Accept': 'application/json' }
        }).then(function () {
            history = [];
            renderHistory();
            if (typeof toastr !== 'undefined') toastr.success('চ্যাট ক্লিয়ার');
        });
    });

    document.getElementById('gaw-refresh').addEventListener('click', function () {
        fetch(routes.refresh, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': routes.csrf, 'Accept': 'application/json' }
        }).then(function () {
            if (typeof toastr !== 'undefined') toastr.success('সাইট ডাটা রিফ্রেশ হয়েছে');
        });
    });

    renderHistory();
})();
</script>
