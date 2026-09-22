@php
    $gccEnabled = app(\App\Services\GeminiCustomerContextService::class)->isEnabled();
    $gccWelcome = app(\App\Services\GeminiCustomerContextService::class)->welcomeMessage();
    $gccHistory = session('gemini_customer_chat_history', []);
    $gccCustomer = auth('customer')->user();
@endphp

@if($gccEnabled)
<style>
#gcc-widget { font-family: inherit; }
#gcc-widget * { box-sizing: border-box; }

/* FLOATING TRIGGER BUTTON */
#gcc-toggle {
    position: fixed;
    right: 20px;
    bottom: 75px;
    z-index: 99990;
    width: 58px;
    height: 58px;
    border-radius: 50%;
    border: none;
    cursor: pointer;
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    color: #ffffff;
    box-shadow: 0 10px 28px rgba(5, 150, 105, 0.45);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    padding: 0;
}
#gcc-toggle:hover {
    transform: scale(1.08) translateY(-2px);
    box-shadow: 0 14px 34px rgba(5, 150, 105, 0.55);
}
#gcc-toggle.open {
    opacity: 0;
    pointer-events: none;
    transform: scale(0.85);
}
#gcc-toggle .gcc-toggle-icon {
    width: 26px;
    height: 26px;
    display: block;
}

/* CHAT MODAL / PANEL */
#gcc-panel {
    position: fixed;
    right: 20px;
    bottom: 145px;
    z-index: 99989;
    width: 390px;
    max-width: calc(100vw - 24px);
    height: 540px;
    max-height: calc(100vh - 160px);
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 20px 60px -10px rgba(15, 23, 42, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transform: scale(0.92) translateY(24px);
    opacity: 0;
    pointer-events: none;
    transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
}
#gcc-panel.open {
    transform: scale(1) translateY(0);
    opacity: 1;
    pointer-events: auto;
}

/* HEADER */
#gcc-header {
    background: linear-gradient(135deg, #064e3b 0%, #047857 100%);
    color: #ffffff;
    padding: 16px 18px;
    flex-shrink: 0;
}
#gcc-header h5 {
    color: #ffffff !important;
    margin: 0;
    font-size: 16px;
    font-weight: 700;
    letter-spacing: -0.01em;
}
#gcc-header .gcc-header-sub {
    color: #a7f3d0 !important;
    margin: 4px 0 0;
    font-size: 11.5px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.gcc-online-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #34d399;
    box-shadow: 0 0 8px #34d399;
    display: inline-block;
}
#gcc-header-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
}
.gcc-header-icon-wrap {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(6px);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
#gcc-close {
    background: rgba(255, 255, 255, 0.12);
    border: none;
    color: #ffffff;
    font-size: 14px;
    cursor: pointer;
    line-height: 1;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}
#gcc-close:hover {
    background: rgba(255, 255, 255, 0.25);
}

/* QUICK ACTION CHIPS */
#gcc-chips {
    display: flex;
    flex-wrap: nowrap;
    overflow-x: auto;
    gap: 6px;
    padding: 10px 14px;
    background: #f0fdf4;
    border-bottom: 1px solid #dcfce7;
    scrollbar-width: none;
}
#gcc-chips::-webkit-scrollbar { display: none; }
#gcc-chips button {
    border: 1px solid #a7f3d0;
    background: #ffffff;
    color: #065f46;
    border-radius: 20px;
    padding: 5px 12px;
    font-size: 11.5px;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
    flex-shrink: 0;
    transition: all 0.15s ease;
}
#gcc-chips button:hover {
    background: #059669;
    color: #ffffff;
    border-color: #059669;
}

/* MESSAGES LIST */
#gcc-messages {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    background: #f8fafc;
    scroll-behavior: smooth;
}
.gcc-msg {
    display: flex;
    gap: 8px;
    margin-bottom: 14px;
    animation: gccSlide 0.2s ease-out;
}
@keyframes gccSlide {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}
.gcc-msg.user { flex-direction: row-reverse; }

.gcc-avatar {
    width: 30px;
    height: 30px;
    border-radius: 10px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    color: #ffffff;
}
.gcc-msg.user .gcc-avatar {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
}
.gcc-msg.bot .gcc-avatar {
    background: linear-gradient(135deg, #059669, #10b981);
}

.gcc-bubble {
    max-width: 82%;
    padding: 10px 14px;
    border-radius: 15px;
    font-size: 13px;
    line-height: 1.55;
    white-space: pre-wrap;
    word-break: break-word;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
}
.gcc-msg.user .gcc-bubble {
    background: linear-gradient(135deg, #059669, #10b981);
    color: #ffffff;
    border-bottom-right-radius: 3px;
}
.gcc-msg.bot .gcc-bubble {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    color: #1e293b;
    border-bottom-left-radius: 3px;
}

/* CLICKABLE LINKS IN CHAT */
.gcc-chat-link {
    color: #0284c7 !important;
    text-decoration: underline !important;
    font-weight: 600 !important;
    word-break: break-all;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.gcc-chat-link:hover {
    color: #0369a1 !important;
}
.gcc-msg.user .gcc-chat-link {
    color: #ecfdf5 !important;
}

/* PRODUCT RECOMMENDATION CARDS */
.gcc-products {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 8px;
    max-width: 90%;
}
.gcc-product-card {
    display: flex;
    gap: 10px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 8px 10px;
    text-decoration: none !important;
    color: inherit;
    transition: all 0.2s ease;
}
.gcc-product-card:hover {
    border-color: #10b981;
    box-shadow: 0 4px 14px rgba(5, 150, 105, 0.12);
    transform: translateY(-1px);
    color: inherit;
}
.gcc-product-card img {
    width: 48px;
    height: 48px;
    object-fit: cover;
    border-radius: 8px;
    flex-shrink: 0;
    border: 1px solid #f1f5f9;
}
.gcc-product-card .gcc-pname {
    font-size: 12px;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 3px;
    line-height: 1.3;
}
.gcc-product-card .gcc-pprice {
    font-size: 13px;
    font-weight: 700;
    color: #059669;
    margin: 0;
}
.gcc-product-card .gcc-pstock {
    font-size: 10.5px;
    color: #64748b;
    margin: 2px 0 0;
}

/* TYPING INDICATOR */
#gcc-typing {
    display: none;
    padding: 4px 16px 8px;
    font-size: 12px;
    color: #059669;
    font-weight: 600;
    background: #f8fafc;
    align-items: center;
    gap: 6px;
}
#gcc-typing.show { display: flex; }

/* INPUT BOX */
#gcc-input-wrap {
    padding: 12px 14px;
    border-top: 1px solid #e2e8f0;
    background: #ffffff;
    flex-shrink: 0;
}
#gcc-form {
    display: flex;
    gap: 8px;
    align-items: flex-end;
    background: #f8fafc;
    border: 1.5px solid #cbd5e1;
    border-radius: 14px;
    padding: 6px 8px 6px 12px;
    transition: all 0.2s ease;
}
#gcc-form:focus-within {
    background: #ffffff;
    border-color: #059669;
    box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.12);
}
#gcc-input {
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
#gcc-send {
    background: linear-gradient(135deg, #059669, #10b981);
    border: none;
    color: #ffffff;
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
#gcc-send:hover:not(:disabled) {
    transform: scale(1.03);
    box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
}
#gcc-send:disabled {
    opacity: 0.55;
    cursor: not-allowed;
}

/* COMPLAINT MODAL PANEL */
#gcc-complaint-panel {
    display: none;
    position: absolute;
    inset: 0;
    background: #ffffff;
    z-index: 5;
    flex-direction: column;
    padding: 18px;
    overflow-y: auto;
}
#gcc-complaint-panel.show { display: flex; }
#gcc-complaint-panel h6 {
    font-weight: 700;
    font-size: 15px;
    color: #1e293b;
    margin-bottom: 12px;
}
#gcc-complaint-panel label {
    font-size: 12px;
    font-weight: 600;
    color: #475569;
    margin-bottom: 4px;
    display: block;
}
#gcc-complaint-panel input, #gcc-complaint-panel textarea {
    width: 100%;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 8px 10px;
    font-size: 13px;
    margin-bottom: 10px;
    outline: none;
}
#gcc-complaint-panel input:focus, #gcc-complaint-panel textarea:focus {
    border-color: #059669;
}
#gcc-complaint-actions {
    display: flex;
    gap: 8px;
    margin-top: 8px;
}
#gcc-complaint-actions button {
    flex: 1;
    padding: 9px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    border: none;
}
#gcc-complaint-submit { background: #059669; color: #fff; }
#gcc-complaint-cancel { background: #f1f5f9; color: #475569; }

@media (max-width: 480px) {
    #gcc-panel { right: 12px; bottom: 130px; width: calc(100vw - 24px); height: 68vh; }
    #gcc-toggle { right: 16px; bottom: 70px; width: 52px; height: 52px; }
}
</style>

<div id="gcc-widget">
    <div id="gcc-panel">
        <div id="gcc-header">
            <div id="gcc-header-top">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div class="gcc-header-icon-wrap" aria-hidden="true">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div>
                        <h5>লাইভ সহায়তা</h5>
                        <p class="gcc-header-sub"><span class="gcc-online-dot"></span> অনলাইন আছেন · প্রশ্ন করুন</p>
                    </div>
                </div>
                <button type="button" id="gcc-close" aria-label="Close">✕</button>
            </div>
        </div>

        <div id="gcc-chips">
            <button type="button" data-q="৫০০০ টাকার মধ্যে ভালো প্রোডাক্ট দেখান">🔍 প্রোডাক্ট সার্চ</button>
            <button type="button" data-q="আমার অর্ডার ট্র্যাক করতে চাই">📦 অর্ডার ট্র্যাক</button>
            <button type="button" id="gcc-open-complaint">📝 কমপ্লেইন</button>
            <button type="button" data-q="রিটার্ন ও রিফান্ড পলিসি কী?">💰 রিফান্ড গাইড</button>
        </div>

        <div id="gcc-messages"></div>
        <div id="gcc-typing"><i class="fas fa-spinner fa-spin"></i> উত্তর তৈরি হচ্ছে...</div>

        <div id="gcc-input-wrap">
            <form id="gcc-form">
                <textarea id="gcc-input" rows="1" placeholder="যেকোনো প্রশ্ন লিখুন..." maxlength="2000"></textarea>
                <button type="submit" id="gcc-send"><i class="fas fa-paper-plane"></i></button>
            </form>
        </div>

        <div id="gcc-complaint-panel">
            <h6>কমপ্লেইন / সমস্যা জানান</h6>
            <label>নাম *</label>
            <input type="text" id="gcc-c-name" value="{{ $gccCustomer->name ?? '' }}" maxlength="255">
            <label>মোবাইল *</label>
            <input type="text" id="gcc-c-phone" value="{{ $gccCustomer->phone ?? '' }}" maxlength="20">
            <label>অর্ডার / ইনভয়েস নং (ঐচ্ছিক)</label>
            <input type="text" id="gcc-c-order" maxlength="50">
            <label>সমস্যার বিস্তারিত *</label>
            <textarea id="gcc-c-desc" rows="4" maxlength="5000" placeholder="সমস্যাটি বিস্তারিত লিখুন..."></textarea>
            <div id="gcc-complaint-actions">
                <button type="button" class="gcc-complaint-cancel" id="gcc-complaint-cancel">বাতিল</button>
                <button type="button" class="gcc-complaint-submit" id="gcc-complaint-submit">জমা দিন</button>
            </div>
        </div>
    </div>

    <button type="button" id="gcc-toggle" title="লাইভ সহায়তা" aria-label="Open live chat">
        <i class="fas fa-comments"></i>
    </button>
</div>

<script>
(function () {
    var history = @json($gccHistory);
    var welcome = @json($gccWelcome);
    var routes = {
        send: @json(route('customer.gemini_chat.send')),
        clear: @json(route('customer.gemini_chat.clear')),
        complaint: @json(route('customer.gemini_chat.complaint')),
        csrf: @json(csrf_token())
    };

    var panel = document.getElementById('gcc-panel');
    var toggle = document.getElementById('gcc-toggle');
    var messages = document.getElementById('gcc-messages');
    var typing = document.getElementById('gcc-typing');
    var input = document.getElementById('gcc-input');
    var form = document.getElementById('gcc-form');
    var sendBtn = document.getElementById('gcc-send');
    var isOpen = false;
    var welcomed = history.length > 0;

    function esc(t) {
        var d = document.createElement('div');
        d.textContent = t;
        return d.innerHTML;
    }

    // Convert markdown links & raw URLs into safe clickable links with target="_blank"
    function formatChatLinks(text) {
        if (!text) return '';
        var safe = esc(text);

        // 1. Markdown style: [Label](url)
        safe = safe.replace(/\[([^\]]+)\]\((https?:\/\/[^\s\)]+|\/[^\s\)]+)\)/gi, function(match, label, url) {
            return '<a href="' + url + '" target="_blank" rel="noopener noreferrer" class="gcc-chat-link" onclick="event.stopPropagation();">' + label + ' <i class="fas fa-external-link-alt" style="font-size:10px;"></i></a>';
        });

        // 2. Raw absolute URLs (http:// or https://)
        safe = safe.replace(/(?<!href=["'])(https?:\/\/[^\s<]+)/gi, function(url) {
            var cleanUrl = url.replace(/[.,;!?]+$/, '');
            return '<a href="' + cleanUrl + '" target="_blank" rel="noopener noreferrer" class="gcc-chat-link" onclick="event.stopPropagation();">' + cleanUrl + ' <i class="fas fa-external-link-alt" style="font-size:10px;"></i></a>';
        });

        // 3. Internal store paths (e.g. /order-track/...)
        safe = safe.replace(/(?<!href=["'])(?<!\/)(\/(?:order-track|customer|product|shop|track|complaint)[^\s<]*)/gi, function(url) {
            var cleanUrl = url.replace(/[.,;!?]+$/, '');
            return '<a href="' + cleanUrl + '" target="_blank" rel="noopener noreferrer" class="gcc-chat-link" onclick="event.stopPropagation();">' + cleanUrl + ' <i class="fas fa-external-link-alt" style="font-size:10px;"></i></a>';
        });

        return safe;
    }

    function renderProducts(products) {
        if (!products || !products.length) return '';
        var html = '<div class="gcc-products">';
        products.slice(0, 5).forEach(function (p) {
            html += '<a class="gcc-product-card" href="' + esc(p.url) + '" target="_blank" rel="noopener noreferrer" onclick="event.stopPropagation();">';
            if (p.image) html += '<img src="' + esc(p.image) + '" alt="">';
            html += '<div><p class="gcc-pname">' + esc(p.name) + '</p>';
            html += '<p class="gcc-pprice">৳' + esc(String(p.price)) + '</p>';
            html += '<p class="gcc-pstock">' + (p.in_stock ? '✓ স্টকে আছে' : 'স্টক শেষ') + '</p></div></a>';
        });
        return html + '</div>';
    }

    function appendBubble(role, text, products, scroll) {
        var div = document.createElement('div');
        div.className = 'gcc-msg ' + (role === 'user' ? 'user' : 'bot');
        var avatar = role === 'user' ? '<i class="fas fa-user"></i>' : '<i class="fas fa-robot"></i>';
        var extra = role === 'bot' && products ? renderProducts(products) : '';
        div.innerHTML = '<div class="gcc-avatar">' + avatar + '</div><div><div class="gcc-bubble">' + formatChatLinks(text) + '</div>' + extra + '</div>';
        messages.appendChild(div);

        if (scroll !== false) {
            if (role === 'user') {
                messages.scrollTop = messages.scrollHeight;
            } else {
                // ⭐ SMART SCROLL: Align to top of the new AI response so user reads from the beginning!
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

    function renderHistory() {
        messages.innerHTML = '';
        if (!history.length) {
            appendBubble('bot', welcome, null, false);
            welcomed = true;
            return;
        }
        history.forEach(function (m) {
            appendBubble(m.role === 'user' ? 'user' : 'bot', m.text, m.products || null, false);
        });
        messages.scrollTop = messages.scrollHeight;
    }

    function openPanel() {
        isOpen = true;
        panel.classList.add('open');
        toggle.classList.add('open');
        if (!welcomed) { appendBubble('bot', welcome); welcomed = true; }
        setTimeout(function () { input.focus(); }, 200);
    }

    function closePanel() {
        isOpen = false;
        panel.classList.remove('open');
        toggle.classList.remove('open');
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
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': routes.csrf, 'Accept': 'application/json' },
            body: JSON.stringify({ message: text })
        })
        .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
        .then(function (res) {
            if (res.ok && res.data.success) {
                appendBubble('bot', res.data.reply, res.data.products || []);
                history.push({ role: 'model', text: res.data.reply, products: res.data.products });
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

    toggle.addEventListener('click', function () { isOpen ? closePanel() : openPanel(); });
    document.getElementById('gcc-close').addEventListener('click', closePanel);
    form.addEventListener('submit', function (e) { e.preventDefault(); sendMessage(input.value); });
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); form.dispatchEvent(new Event('submit')); }
    });

    input.addEventListener('input', function () {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 90) + 'px';
    });

    document.querySelectorAll('#gcc-chips [data-q]').forEach(function (btn) {
        btn.addEventListener('click', function () { sendMessage(btn.getAttribute('data-q')); });
    });

    document.getElementById('gcc-open-complaint').addEventListener('click', function () {
        document.getElementById('gcc-complaint-panel').classList.add('show');
    });
    document.getElementById('gcc-complaint-cancel').addEventListener('click', function () {
        document.getElementById('gcc-complaint-panel').classList.remove('show');
    });
    document.getElementById('gcc-complaint-submit').addEventListener('click', function () {
        var payload = {
            name: document.getElementById('gcc-c-name').value.trim(),
            phone: document.getElementById('gcc-c-phone').value.trim(),
            order_id: document.getElementById('gcc-c-order').value.trim(),
            description: document.getElementById('gcc-c-desc').value.trim()
        };
        if (!payload.name || !payload.phone || !payload.description) {
            alert('নাম, মোবাইল ও বিস্তারিত পূরণ করুন।');
            return;
        }
        fetch(routes.complaint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': routes.csrf, 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(function (r) { return r.json(); })
        .then(function (d) {
            document.getElementById('gcc-complaint-panel').classList.remove('show');
            appendBubble('bot', d.success ? d.message : ('⚠️ ' + (d.message || 'ব্যর্থ')));
            if (d.success) {
                document.getElementById('gcc-c-desc').value = '';
            }
        });
    });

    renderHistory();
})();
</script>
@endif
