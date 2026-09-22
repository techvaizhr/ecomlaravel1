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
    right: 20px;
    bottom: 80px;
    z-index: 99990;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    border: none;
    cursor: pointer;
    background: var(--gaw-gradient);
    color: #fff;
    box-shadow: 0 8px 24px rgba(139, 92, 246, 0.45);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    padding: 0;
}
#gaw-toggle:hover {
    transform: scale(1.08) translateY(-2px);
    box-shadow: 0 12px 30px rgba(139, 92, 246, 0.6);
}
#gaw-toggle.open {
    background: #1e293b;
    box-shadow: 0 6px 20px rgba(15, 23, 42, 0.4);
}
#gaw-toggle svg {
    width: 24px;
    height: 24px;
    display: block;
    fill: none;
    stroke: currentColor;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
}

/* SIDE CHAT DRAWER PANEL */
#gaw-panel {
    position: fixed;
    top: 0;
    right: 0;
    width: 400px;
    max-width: 100vw;
    height: 100vh;
    z-index: 99989;
    background: #ffffff;
    box-shadow: -10px 0 50px rgba(15, 23, 42, 0.18);
    display: flex;
    flex-direction: column;
    transform: translateX(100%);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
#gaw-panel.open { transform: translateX(0); }

/* PANEL HEADER */
#gaw-header {
    background: linear-gradient(135deg, #090d16 0%, #1e1b4b 100%);
    color: #fff;
    padding: 14px 16px;
    flex-shrink: 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}
#gaw-header h5 {
    margin: 0;
    font-size: 14.5px;
    font-weight: 700;
    color: #ffffff !important;
    display: flex;
    align-items: center;
    gap: 7px;
}
#gaw-header p {
    margin: 3px 0 0;
    font-size: 11px;
    color: #94a3b8 !important;
}
.gaw-status-dot {
    width: 7px;
    height: 7px;
    background: #22c55e;
    border-radius: 50%;
    display: inline-block;
    box-shadow: 0 0 7px #22c55e;
}
#gaw-header-actions {
    display: flex;
    gap: 6px;
    margin-top: 10px;
    flex-wrap: wrap;
}
#gaw-header-actions button, #gaw-header-actions a {
    font-size: 11px;
    padding: 4px 10px;
    border-radius: 16px;
    border: 1px solid rgba(255,255,255,0.18);
    background: rgba(255,255,255,0.08);
    color: #f1f5f9;
    cursor: pointer;
    text-decoration: none;
    line-height: 1.3;
    display: inline-flex;
    align-items: center;
    gap: 4px;
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
    padding: 14px;
    background: #f8fafc;
    scroll-behavior: smooth;
}
#gaw-messages .gaw-msg {
    display: flex;
    gap: 8px;
    margin-bottom: 12px;
    animation: gawSlide 0.2s ease-out;
}
@keyframes gawSlide {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}
#gaw-messages .gaw-msg.user { flex-direction: row-reverse; }

#gaw-messages .gaw-avatar {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    color: #fff;
}
#gaw-messages .gaw-msg.user .gaw-avatar {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
}
#gaw-messages .gaw-msg.bot .gaw-avatar {
    background: var(--gaw-gradient);
}
#gaw-messages .gaw-avatar svg {
    width: 15px;
    height: 15px;
    stroke: currentColor;
    stroke-width: 2;
    fill: none;
}

#gaw-messages .gaw-bubble {
    max-width: 84%;
    padding: 8px 12px;
    border-radius: 13px;
    font-size: 12.5px;
    line-height: 1.5;
    white-space: pre-wrap;
    word-break: break-word;
    box-shadow: 0 1px 4px rgba(15, 23, 42, 0.04);
}
#gaw-messages .gaw-msg.user .gaw-bubble {
    background: linear-gradient(135deg, #4f46e5, #6366f1);
    color: #fff;
    border-bottom-right-radius: 2px;
}
#gaw-messages .gaw-msg.bot .gaw-bubble {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    color: #1e293b;
    border-bottom-left-radius: 2px;
}

/* CLICKABLE LINKS */
.gaw-chat-link {
    color: #4f46e5 !important;
    text-decoration: underline !important;
    font-weight: 600 !important;
    word-break: break-all;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.gaw-chat-link:hover {
    color: #4338ca !important;
}
#gaw-messages .gaw-msg.user .gaw-chat-link {
    color: #e0e7ff !important;
}

/* EMPTY STATE */
#gaw-empty {
    text-align: center;
    padding: 20px 8px;
    color: #64748b;
}
.gaw-empty-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: #eef2ff;
    color: var(--gaw-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
}
.gaw-empty-icon svg {
    width: 24px;
    height: 24px;
    stroke: currentColor;
    stroke-width: 2;
    fill: none;
}
#gaw-empty strong {
    color: #1e293b;
    font-size: 13.5px;
    display: block;
    margin-bottom: 3px;
}
#gaw-empty p {
    font-size: 11.5px;
    color: #64748b;
    margin-bottom: 14px;
}
#gaw-suggestions {
    display: flex;
    flex-direction: column;
    gap: 6px;
    text-align: left;
}
#gaw-suggestions button {
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #334155;
    border-radius: 10px;
    padding: 7px 10px;
    font-size: 11.5px;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 7px;
    transition: all 0.15s ease;
}
#gaw-suggestions button:hover {
    border-color: #818cf8;
    background: #f5f7ff;
    color: var(--gaw-primary);
    transform: translateX(2px);
}

/* TYPING INDICATOR */
#gaw-typing {
    display: none;
    padding: 4px 14px 8px;
    font-size: 11.5px;
    color: var(--gaw-primary);
    font-weight: 600;
    background: #f8fafc;
    align-items: center;
    gap: 5px;
}
#gaw-typing.show { display: flex; }

/* INPUT SECTION */
#gaw-input-wrap {
    padding: 10px 12px;
    border-top: 1px solid #e2e8f0;
    background: #ffffff;
    flex-shrink: 0;
}
#gaw-input-form {
    display: flex;
    align-items: flex-end;
    gap: 6px;
    background: #f8fafc;
    border: 1.5px solid #cbd5e1;
    border-radius: 12px;
    padding: 5px 6px 5px 10px;
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
    font-size: 12.5px;
    max-height: 80px;
    min-height: 26px;
    outline: none;
    color: #1e293b;
    padding: 3px 0;
    line-height: 1.4;
}
#gaw-send {
    background: var(--gaw-gradient);
    border: none;
    color: #fff;
    border-radius: 8px;
    padding: 5px 12px;
    font-weight: 700;
    font-size: 12px;
    cursor: pointer;
    flex-shrink: 0;
    height: 30px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all 0.2s ease;
}
#gaw-send svg {
    width: 14px;
    height: 14px;
    stroke: currentColor;
    stroke-width: 2;
    fill: none;
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
    #gaw-toggle { right: 14px; bottom: 75px; width: 44px; height: 44px; }
}
</style>

<div id="gemini-admin-widget" aria-hidden="true">
    <div id="gaw-overlay"></div>

    <aside id="gaw-panel" role="dialog" aria-label="Gemini AI Assistant">
        <div id="gaw-header">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h5>
                        <svg style="width:16px;height:16px;display:inline-block;vertical-align:middle;stroke:currentColor;fill:none;stroke-width:2;" viewBox="0 0 24 24"><path d="M12 8V4H8"/><rect x="4" y="8" width="16" height="12" rx="2"/><path d="M2 14h2"/><path d="M20 14h2"/><path d="M15 13v2"/><path d="M9 13v2"/></svg>
                        Gemini Assistant <span class="gaw-status-dot"></span>
                    </h5>
                    <p>স্টোর, সেলস ও সেটিংস সম্পর্কে যেকোনো প্রশ্ন করুন</p>
                </div>
                <button type="button" id="gaw-close-x" style="background:rgba(255,255,255,0.12);border:none;color:#fff;font-size:13px;cursor:pointer;border-radius:50%;width:26px;height:26px;display:flex;align-items:center;justify-content:center;transition:background 0.2s;" aria-label="Close">✕</button>
            </div>
            <div id="gaw-header-actions">
                <button type="button" id="gaw-refresh">🔄 রিফ্রেশ</button>
                <button type="button" id="gaw-clear">🗑️ মুছুন</button>
                <a href="{{ route('admin.gemini_chat.index') }}" target="_blank">↗️ ফুল পেজ</a>
            </div>
        </div>

        <div id="gaw-messages"></div>
        <div id="gaw-typing">
            <svg style="width:13px;height:13px;animation:spin 1s linear infinite" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" stroke-dasharray="32" stroke-dashoffset="12"/></svg>
            <span>উত্তর ভাবছে...</span>
        </div>

        <div id="gaw-input-wrap">
            <form id="gaw-input-form">
                <textarea id="gaw-input" rows="1" placeholder="প্রশ্ন টাইপ করুন..." maxlength="4000"></textarea>
                <button type="submit" id="gaw-send" aria-label="Send">
                    <svg viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>
            </form>
        </div>
    </aside>

    {{-- CLEAN CRISP SVG FLOATING TOGGLE --}}
    <button type="button" id="gaw-toggle" title="Gemini Assistant" aria-label="Open Gemini Assistant">
        <svg id="gaw-toggle-icon" viewBox="0 0 24 24">
            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
        </svg>
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

    var botSvg = '<svg viewBox="0 0 24 24"><path d="M12 8V4H8"/><rect x="4" y="8" width="16" height="12" rx="2"/><path d="M2 14h2"/><path d="M20 14h2"/><path d="M15 13v2"/><path d="M9 13v2"/></svg>';
    var userSvg = '<svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
    var extLinkSvg = '<svg style="width:11px;height:11px;display:inline-block;vertical-align:middle;margin-left:2px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>';
    var chatIconSvg = '<svg viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>';
    var closeIconSvg = '<svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';

    function formatChatLinks(rawText) {
        if (!rawText) return '';
        var div = document.createElement('div');
        div.textContent = rawText;
        var text = div.innerHTML;

        var links = [];

        // 1. Markdown Links [label](url)
        text = text.replace(/\[([^\]]+)\]\((https?:\/\/[^\s\)\"\'<>]+|\/[^\s\)\"\'<>]+)\)/gi, function(match, label, url) {
            var idx = links.length;
            links.push('<a href="' + url + '" target="_blank" rel="noopener noreferrer" class="gaw-chat-link" onclick="event.stopPropagation();">' + label + ' ' + extLinkSvg + '</a>');
            return '___GAW_LINK_' + idx + '___';
        });

        // 2. Full absolute URLs (http:// or https://)
        text = text.replace(/(https?:\/\/[^\s<>"']+)/gi, function(url) {
            var cleanUrl = url.replace(/[.,;:!?)]+$/, '');
            var trail = url.slice(cleanUrl.length);
            var idx = links.length;
            links.push('<a href="' + cleanUrl + '" target="_blank" rel="noopener noreferrer" class="gaw-chat-link" onclick="event.stopPropagation();">' + cleanUrl + ' ' + extLinkSvg + '</a>' + trail);
            return '___GAW_LINK_' + idx + '___';
        });

        // 3. Internal relative links like /admin/order/manage
        text = text.replace(/(?<!\/)(\/(?:admin|order|product|customer)[a-zA-Z0-9_\-\/]*)/gi, function(url) {
            var cleanUrl = url.replace(/[.,;:!?)]+$/, '');
            var trail = url.slice(cleanUrl.length);
            var idx = links.length;
            links.push('<a href="' + cleanUrl + '" target="_blank" rel="noopener noreferrer" class="gaw-chat-link" onclick="event.stopPropagation();">' + cleanUrl + ' ' + extLinkSvg + '</a>' + trail);
            return '___GAW_LINK_' + idx + '___';
        });

        // 4. Restore links
        for (var i = 0; i < links.length; i++) {
            text = text.replace('___GAW_LINK_' + i + '___', links[i]);
        }

        return text;
    }

    function renderHistory() {
        messages.innerHTML = '';
        if (!history.length) {
            messages.innerHTML =
                '<div id="gaw-empty">' +
                '<div class="gaw-empty-icon">' + botSvg + '</div>' +
                '<strong>কী জানতে চান?</strong>' +
                '<p>অর্ডার, প্রোডাক্ট, ফ্রড চেক বা সেটিংস — যেকোনো কিছু জিজ্ঞাসা করুন।</p>' +
                '<div id="gaw-suggestions">' +
                '<button type="button" data-q="আজ কতটি নতুন অর্ডার এসেছে?">📦 আজকের মোট অর্ডার কত?</button>' +
                '<button type="button" data-q="Pending product কিভাবে approve করব?">✅ প্রোডাক্ট অ্যাপ্রুভ করার নিয়ম</button>' +
                '<button type="button" data-q="Fraud check কিভাবে করব?">🛡️ কাস্টমার ফ্রড চেক কীভাবে করে?</button>' +
                '</div></div>';
            bindSuggestions();
            return;
        }
        history.forEach(function (m) {
            appendBubble(m.role === 'user' ? 'user' : 'bot', m.text, false);
        });
        messages.scrollTop = messages.scrollHeight;
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
        var avatar = isUser ? userSvg : botSvg;
        div.innerHTML =
            '<div class="gaw-avatar">' + avatar + '</div>' +
            '<div class="gaw-bubble">' + formatChatLinks(text) + '</div>';
        messages.appendChild(div);

        if (scroll !== false) {
            if (isUser) {
                messages.scrollTop = messages.scrollHeight;
            } else {
                // Smooth scroll to top of new bot message
                setTimeout(function () {
                    var targetTop = div.offsetTop - messages.offsetTop - 8;
                    messages.scrollTo({
                        top: Math.max(0, targetTop),
                        behavior: 'smooth'
                    });
                }, 50);
            }
        }
    }

    function openPanel() {
        isOpen = true;
        panel.classList.add('open');
        overlay.classList.add('show');
        toggle.classList.add('open');
        toggle.innerHTML = closeIconSvg;
        document.getElementById('gemini-admin-widget').setAttribute('aria-hidden', 'false');
        setTimeout(function () { input.focus(); }, 300);
    }

    function closePanel() {
        isOpen = false;
        panel.classList.remove('open');
        overlay.classList.remove('show');
        toggle.classList.remove('open');
        toggle.innerHTML = chatIconSvg;
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
        this.style.height = Math.min(this.scrollHeight, 80) + 'px';
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
