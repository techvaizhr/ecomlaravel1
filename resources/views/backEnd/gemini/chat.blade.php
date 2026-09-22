@extends('backEnd.layouts.master')
@section('title', 'Gemini AI Assistant')

@section('css')
<style>
:root {
    --ai-primary: #6366f1;
    --ai-primary-hover: #4f46e5;
    --ai-accent: #a855f7;
    --ai-accent-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%);
    --ai-dark-header: linear-gradient(135deg, #090d16 0%, #111827 50%, #1e1b4b 100%);
    --ai-bg-light: #f8fafc;
    --ai-border: #e2e8f0;
    --ai-text-main: #0f172a;
    --ai-text-muted: #64748b;
}

.gemini-page-container {
    max-width: 1040px;
    margin: 0 auto;
    padding: 10px 0 30px;
}

/* TOP HERO HEADER */
.gemini-hero-header {
    background: var(--ai-dark-header);
    border-radius: 20px;
    padding: 22px 26px;
    color: #ffffff;
    box-shadow: 0 16px 36px -8px rgba(15, 23, 42, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.08);
    position: relative;
    overflow: hidden;
    margin-bottom: 20px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}

.gemini-hero-header::before {
    content: '';
    position: absolute;
    top: -60px;
    right: -60px;
    width: 220px;
    height: 220px;
    background: radial-gradient(circle, rgba(168, 85, 247, 0.3) 0%, rgba(99, 102, 241, 0) 70%);
    filter: blur(20px);
    pointer-events: none;
}

.gemini-hero-left {
    display: flex;
    align-items: center;
    gap: 16px;
    z-index: 1;
}

.gemini-hero-avatar {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    background: var(--ai-accent-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #fff;
    box-shadow: 0 8px 20px rgba(168, 85, 247, 0.4);
    position: relative;
}

.gemini-hero-avatar::after {
    content: '';
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 13px;
    height: 13px;
    background: #22c55e;
    border: 2px solid #090d16;
    border-radius: 50%;
}

.gemini-hero-title h4 {
    margin: 0;
    font-size: 1.3rem;
    font-weight: 800;
    letter-spacing: -0.02em;
    display: flex;
    align-items: center;
    gap: 8px;
    color: #fff;
}

.gemini-hero-title p {
    margin: 4px 0 0;
    font-size: 13px;
    color: #94a3b8;
    max-width: 500px;
    line-height: 1.4;
}

.gemini-hero-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    z-index: 1;
}

.gemini-btn-pill {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: #f1f5f9 !important;
    padding: 7px 14px;
    border-radius: 30px;
    font-size: 12.5px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
    text-decoration: none;
    backdrop-filter: blur(8px);
}

.gemini-btn-pill:hover {
    background: rgba(255, 255, 255, 0.18);
    border-color: rgba(255, 255, 255, 0.3);
    transform: translateY(-1px);
}

/* MAIN CHAT CONTAINER CARD */
.gemini-chat-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    display: flex;
    flex-direction: column;
    height: calc(100vh - 250px);
    min-height: 520px;
    overflow: hidden;
}

/* CHAT MESSAGES SCROLL AREA */
.gemini-chat-area {
    flex: 1;
    overflow-y: auto;
    padding: 24px;
    background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
    scroll-behavior: smooth;
}

/* EMPTY STATE */
.gemini-empty-wrapper {
    max-width: 600px;
    margin: 40px auto 20px;
    text-align: center;
}

.gemini-empty-icon {
    width: 68px;
    height: 68px;
    margin: 0 auto 16px;
    border-radius: 20px;
    background: linear-gradient(135deg, #eef2ff 0%, #f3e8ff 100%);
    border: 1px solid #e0e7ff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    color: var(--ai-primary);
    box-shadow: 0 8px 24px rgba(99, 102, 241, 0.12);
}

.gemini-empty-wrapper h5 {
    font-size: 18px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 6px;
}

.gemini-empty-wrapper p {
    color: #64748b;
    font-size: 13.5px;
    margin-bottom: 24px;
}

.gemini-grid-suggestions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 10px;
    text-align: left;
}

.gemini-card-suggestion {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 12px 14px;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    gap: 10px;
}

.gemini-card-suggestion:hover {
    border-color: #818cf8;
    background: #f5f7ff;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(99, 102, 241, 0.08);
}

.gemini-card-suggestion .sug-icon {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    background: #eef2ff;
    color: var(--ai-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
}

.gemini-card-suggestion .sug-text {
    font-size: 12.5px;
    font-weight: 600;
    color: #334155;
    line-height: 1.35;
}

/* MESSAGE BUBBLES */
.gemini-msg-item {
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
    animation: geminiSlideIn 0.25s ease-out;
}

@keyframes geminiSlideIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}

.gemini-msg-item.user {
    flex-direction: row-reverse;
}

.gemini-bubble-avatar {
    width: 36px;
    height: 36px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
}

.gemini-msg-item.user .gemini-bubble-avatar {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: #fff;
}

.gemini-msg-item.model .gemini-bubble-avatar {
    background: var(--ai-accent-gradient);
    color: #fff;
}

.gemini-bubble-content {
    max-width: 80%;
    display: flex;
    flex-direction: column;
}

.gemini-msg-item.user .gemini-bubble-content {
    align-items: flex-end;
}

.gemini-msg-item.model .gemini-bubble-content {
    align-items: flex-start;
}

.gemini-bubble-box {
    padding: 13px 17px;
    border-radius: 16px;
    font-size: 13.5px;
    line-height: 1.6;
    word-break: break-word;
    white-space: pre-wrap;
    box-shadow: 0 3px 12px rgba(15, 23, 42, 0.04);
}

.gemini-msg-item.user .gemini-bubble-box {
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
    color: #ffffff;
    border-bottom-right-radius: 4px;
}

.gemini-msg-item.model .gemini-bubble-box {
    background: #ffffff;
    color: #1e293b;
    border: 1px solid #e2e8f0;
    border-bottom-left-radius: 4px;
}

.gemini-bubble-time {
    font-size: 11px;
    color: #94a3b8;
    margin-top: 5px;
    padding: 0 4px;
}

/* TYPING INDICATOR */
.gemini-typing-box {
    display: none;
    padding: 8px 24px 12px;
    font-size: 12.5px;
    color: var(--ai-primary);
    font-weight: 600;
    align-items: center;
    gap: 8px;
    background: #ffffff;
}

.gemini-typing-box.show {
    display: flex;
}

.gemini-typing-dots {
    display: inline-flex;
    gap: 4px;
}

.gemini-typing-dots span {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--ai-primary);
    animation: geminiBounce 1.4s infinite ease-in-out both;
}

.gemini-typing-dots span:nth-child(1) { animation-delay: -0.32s; }
.gemini-typing-dots span:nth-child(2) { animation-delay: -0.16s; }

@keyframes geminiBounce {
    0%, 80%, 100% { transform: scale(0); opacity: 0.4; }
    40% { transform: scale(1); opacity: 1; }
}

/* INPUT DOCK SECTION */
.gemini-input-dock {
    padding: 16px 20px;
    background: #ffffff;
    border-top: 1px solid #f1f5f9;
}

.gemini-input-shell {
    display: flex;
    align-items: flex-end;
    gap: 10px;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 16px;
    padding: 8px 10px 8px 16px;
    transition: all 0.2s ease;
}

.gemini-input-shell:focus-within {
    background: #ffffff;
    border-color: var(--ai-primary);
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
}

.gemini-textarea {
    flex: 1;
    border: none;
    background: transparent;
    font-size: 14px;
    color: #1e293b;
    resize: none;
    min-height: 28px;
    max-height: 140px;
    outline: none;
    padding: 4px 0;
    line-height: 1.5;
}

.gemini-send-button {
    background: var(--ai-accent-gradient);
    border: none;
    color: #ffffff;
    border-radius: 12px;
    padding: 8px 18px;
    font-size: 13.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    flex-shrink: 0;
    height: 38px;
}

.gemini-send-button:hover:not(:disabled) {
    transform: scale(1.02);
    box-shadow: 0 4px 14px rgba(168, 85, 247, 0.35);
}

.gemini-send-button:disabled {
    opacity: 0.55;
    cursor: not-allowed;
}

@media (max-width: 768px) {
    .gemini-chat-card { height: calc(100vh - 210px); }
    .gemini-bubble-content { max-width: 90%; }
    .gemini-hero-header { padding: 16px; }
}
</style>
@endsection

@section('content')
<div class="container-fluid gemini-page-container">
    
    {{-- TOP HERO HEADER --}}
    <div class="gemini-hero-header">
        <div class="gemini-hero-left">
            <div class="gemini-hero-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="gemini-hero-title">
                <h4>Gemini AI Assistant <span class="badge bg-success" style="font-size: 10.5px; font-weight: 600; padding: 3px 8px; border-radius: 20px;">Online</span></h4>
                <p>ওয়েবসাইট ডাটাবেজ, সেলস, অর্ডার ও সেটিংস সম্পর্কিত যেকোনো সাহায্য নিন। বাংলা ও ইংরেজি উভয় ভাষায় সমর্থিত।</p>
            </div>
        </div>
        <div class="gemini-hero-actions">
            <button type="button" class="gemini-btn-pill" id="btn-refresh-context">
                <i class="fas fa-sync-alt"></i> রিফ্রেশ ডাটা
            </button>
            <button type="button" class="gemini-btn-pill" id="btn-clear-chat">
                <i class="fas fa-trash-alt"></i> চ্যাট মুছুন
            </button>
            <a href="{{ route('admin.gemini_ai.edit') }}" class="gemini-btn-pill">
                <i class="fas fa-cog"></i> এপিআই সেটিংস
            </a>
        </div>
    </div>

    {{-- MAIN CHAT CARD --}}
    <div class="gemini-chat-card">
        
        {{-- CHAT MESSAGES SCROLL CONTAINER --}}
        <div class="gemini-chat-area" id="chat-messages">
            @if(empty($history))
            <div class="gemini-empty-wrapper" id="chat-empty">
                <div class="gemini-empty-icon">
                    <i class="fas fa-sparkles"></i>
                </div>
                <h5>স্বাগতম! আজ আমি আপনাকে কীভাবে সহায়তা করতে পারি?</h5>
                <p>নিচের যেকোনো প্রম্পটে ক্লিক করে শুরু করতে পারেন অথবা মেসেজ বক্সে টাইপ করুন:</p>
                <div class="gemini-grid-suggestions">
                    <div class="gemini-card-suggestion" data-prompt="আজ কতটি নতুন অর্ডার এসেছে এবং মোট সেলস কত?">
                        <div class="sug-icon"><i class="fas fa-shopping-bag"></i></div>
                        <div class="sug-text">আজ কতটি নতুন অর্ডার ও মোট সেলস কত?</div>
                    </div>
                    <div class="gemini-card-suggestion" data-prompt="পেন্ডিং প্রোডাক্টগুলো কীভাবে দ্রুত অ্যাপ্রুভ করব?">
                        <div class="sug-icon"><i class="fas fa-check-circle"></i></div>
                        <div class="sug-text">পেন্ডিং প্রোডাক্ট অ্যাপ্রুভ করার গাইডলাইন</div>
                    </div>
                    <div class="gemini-card-suggestion" data-prompt="গ্রাহকের ফ্রড চেক বা রিটার্ন হিস্ট্রি কীভাবে দেখব?">
                        <div class="sug-icon"><i class="fas fa-shield-alt"></i></div>
                        <div class="sug-text">কাস্টমার ফ্রড চেক করার নিয়ম কী?</div>
                    </div>
                    <div class="gemini-card-suggestion" data-prompt="Steadfast বা Pathao কুরিয়ারে বাল্ক বুকিং কীভাবে দেয়?">
                        <div class="sug-icon"><i class="fas fa-truck"></i></div>
                        <div class="sug-text">কুরিয়ার বাল্ক বুকিং পদ্ধতি জানাও</div>
                    </div>
                </div>
            </div>
            @else
                @foreach($history as $msg)
                <div class="gemini-msg-item {{ $msg['role'] === 'user' ? 'user' : 'model' }}">
                    <div class="gemini-bubble-avatar">
                        <i class="fas fa-{{ $msg['role'] === 'user' ? 'user' : 'robot' }}"></i>
                    </div>
                    <div class="gemini-bubble-content">
                        <div class="gemini-bubble-box">{{ $msg['text'] }}</div>
                        @if(!empty($msg['at']))
                        <div class="gemini-bubble-time">{{ $msg['at'] }}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            @endif
        </div>

        {{-- TYPING INDICATOR --}}
        <div class="gemini-typing-box" id="chat-typing">
            <i class="fas fa-robot"></i>
            <span>Gemini উত্তর প্রস্তুত করছে</span>
            <div class="gemini-typing-dots">
                <span></span><span></span><span></span>
            </div>
        </div>

        {{-- INPUT DOCK --}}
        <div class="gemini-input-dock">
            <form id="chat-form">
                @csrf
                <div class="gemini-input-shell">
                    <textarea id="chat-input" class="gemini-textarea" rows="1" placeholder="যেকোনো প্রশ্ন লিখুন... (Enter চাপলে সেন্ড হবে, Shift+Enter এ নতুন লাইন)" maxlength="4000"></textarea>
                    <button type="submit" class="gemini-send-button" id="chat-send-btn">
                        <span>পাঠান</span> <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection

@section('script')
<script>
(function () {
    var $messages = $('#chat-messages');
    var $input = $('#chat-input');
    var $form = $('#chat-form');
    var $sendBtn = $('#chat-send-btn');
    var $typing = $('#chat-typing');

    function escapeHtml(text) {
        return $('<div>').text(text).html();
    }

    function appendMessage(role, text, at) {
        $('#chat-empty').remove();
        var isUser = role === 'user';
        var html = '<div class="gemini-msg-item ' + (isUser ? 'user' : 'model') + '">' +
            '<div class="gemini-bubble-avatar"><i class="fas fa-' + (isUser ? 'user' : 'robot') + '"></i></div>' +
            '<div class="gemini-bubble-content">' +
            '<div class="gemini-bubble-box">' + escapeHtml(text) + '</div>' +
            (at ? '<div class="gemini-bubble-time">' + escapeHtml(at) + '</div>' : '') +
            '</div></div>';
        $messages.append(html);
        $messages.scrollTop($messages[0].scrollHeight);
    }

    function sendMessage(text) {
        text = (text || '').trim();
        if (!text) return;

        appendMessage('user', text, new Date().toLocaleString('sv-SE', { hour12: false }).slice(0, 16).replace('T', ' '));
        $input.val('').css('height', 'auto');
        $sendBtn.prop('disabled', true);
        $typing.addClass('show');

        $.ajax({
            url: '{{ route('admin.gemini_chat.send') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                message: text
            },
            success: function (res) {
                if (res.success && res.reply) {
                    appendMessage('model', res.reply, new Date().toLocaleString('sv-SE', { hour12: false }).slice(0, 16).replace('T', ' '));
                } else if (typeof toastr !== 'undefined') {
                    toastr.error(res.message || 'ব্যর্থ হয়েছে');
                }
            },
            error: function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'মেসেজ পাঠানো ব্যর্থ';
                if (typeof toastr !== 'undefined') toastr.error(msg);
                else alert(msg);
            },
            complete: function () {
                $sendBtn.prop('disabled', false);
                $typing.removeClass('show');
            }
        });
    }

    $form.on('submit', function (e) {
        e.preventDefault();
        sendMessage($input.val());
    });

    $input.on('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            $form.trigger('submit');
        }
    });

    $input.on('input', function () {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 140) + 'px';
    });

    $(document).on('click', '.gemini-card-suggestion', function () {
        sendMessage($(this).data('prompt'));
    });

    $('#btn-clear-chat').on('click', function () {
        if (!confirm('চ্যাট ইতিহাস মুছে ফেলবেন?')) return;
        $.post('{{ route('admin.gemini_chat.clear') }}', { _token: '{{ csrf_token() }}' }, function () {
            $messages.html(
                '<div class="gemini-empty-wrapper" id="chat-empty">' +
                '<div class="gemini-empty-icon"><i class="fas fa-sparkles"></i></div>' +
                '<h5>চ্যাট ক্লিয়ার সম্পন্ন হয়েছে</h5>' +
                '<p>আপনার নতুন প্রশ্ন জিজ্ঞাসা করুন।</p></div>'
            );
            if (typeof toastr !== 'undefined') toastr.success('চ্যাট ক্লিয়ার হয়েছে');
        });
    });

    $('#btn-refresh-context').on('click', function () {
        var $btn = $(this);
        $btn.prop('disabled', true);
        $.post('{{ route('admin.gemini_chat.refresh_context') }}', { _token: '{{ csrf_token() }}' }, function () {
            if (typeof toastr !== 'undefined') toastr.success('সাইট ডাটা রিফ্রেশ হয়েছে');
        }).always(function () { $btn.prop('disabled', false); });
    });

    $messages.scrollTop($messages[0].scrollHeight);
})();
</script>
@endsection
