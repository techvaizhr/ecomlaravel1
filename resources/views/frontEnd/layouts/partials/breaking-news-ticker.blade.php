@php
    $breakingNewsText = trim(preg_replace('/\s+/u', ' ', (string) (optional($generalsetting)->top_headline ?? '')));
    $showNewsTicker = $breakingNewsText !== ''
        && (int) (optional($generalsetting)->news_ticker_enabled ?? 0) === 1;
    $tickerBg = optional($generalsetting)->primary_color ?? '#0d6efd';
    $tickerTextCol = '#ffffff';
    $tickerLabelBg = optional($generalsetting)->secodery_color ?? '#198754';
    $tickerLabelTxt = '#ffffff';
    $tickerSpeed = max(20, min(50, (int) (strlen($breakingNewsText) / 4)));
@endphp
@if($showNewsTicker)
<div class="news-ticker-bar" id="newsTickerBar" data-text="{{ e($breakingNewsText) }}" style="background:{{ $tickerBg }}; color:{{ $tickerTextCol }};">
    <div class="news-ticker-label" style="background:{{ $tickerLabelBg }}; color:{{ $tickerLabelTxt }};">
        <i class="fas fa-bolt" style="margin-right:5px;"></i> Today's Special
    </div>
    <div class="news-ticker-track">
        <div class="news-ticker-inner" id="newsTickerInner" style="animation-duration: {{ $tickerSpeed }}s;">
            @foreach([1, 2] as $copy)
                <span class="news-ticker-item" style="color:{{ $tickerTextCol }};">{{ $breakingNewsText }}</span>
            @endforeach
        </div>
    </div>
    <button type="button" class="news-ticker-close" id="newsTickerClose" title="Close" style="color:{{ $tickerTextCol }};">
        <i class="fas fa-times"></i>
    </button>
</div>
<style>
    .news-ticker-bar {
        width: 100%;
        background: {{ $tickerBg }};
        color: {{ $tickerTextCol }};
        display: flex;
        align-items: center;
        height: 34px;
        overflow: hidden;
        position: relative;
        z-index: 10000;
        flex-shrink: 0;
        box-sizing: border-box;
    }
    .news-ticker-label {
        flex: 0 0 auto;
        background: {{ $tickerLabelBg }};
        color: {{ $tickerLabelTxt }} !important;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        padding: 0 14px;
        height: 100%;
        display: flex;
        align-items: center;
        white-space: nowrap;
        position: relative;
        z-index: 1;
    }
    .news-ticker-label::after {
        content: '';
        position: absolute;
        right: -10px;
        top: 0;
        width: 0;
        height: 0;
        border-top: 17px solid transparent;
        border-bottom: 17px solid transparent;
        border-left: 10px solid {{ $tickerLabelBg }};
        z-index: 2;
    }
    .news-ticker-track {
        flex: 1 1 auto;
        overflow: hidden;
        height: 100%;
        display: flex;
        align-items: center;
        padding-left: 18px;
        position: relative;
        min-width: 0;
    }
    .news-ticker-inner {
        display: flex;
        align-items: center;
        white-space: nowrap;
        animation: tickerScroll 30s linear infinite;
        will-change: transform;
    }
    .news-ticker-inner:hover {
        animation-play-state: paused;
    }
    .news-ticker-item {
        display: inline-flex;
        align-items: center;
        font-size: 13px;
        font-weight: 500;
        color: {{ $tickerTextCol }} !important;
        padding-right: 60px;
        white-space: nowrap;
    }
    .news-ticker-item a {
        color: {{ $tickerTextCol }} !important;
        text-decoration: none;
    }
    .news-ticker-item a:hover {
        color: #ffd166 !important;
        text-decoration: underline;
    }
    .news-ticker-item::after {
        display: none !important;
    }
    @keyframes tickerScroll {
        0%   { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    .news-ticker-close {
        flex: 0 0 auto;
        background: transparent;
        border: none;
        color: {{ $tickerTextCol }} !important;
        opacity: 0.75;
        font-size: 15px;
        padding: 0 12px;
        height: 100%;
        cursor: pointer;
        display: flex;
        align-items: center;
        transition: opacity 0.2s;
    }
    .news-ticker-close:hover {
        opacity: 1;
    }
    @media (max-width: 767px) {
        .news-ticker-bar {
            height: 30px;
        }
        .news-ticker-label {
            font-size: 10px;
            padding: 0 10px;
        }
        .news-ticker-label::after {
            border-top-width: 15px;
            border-bottom-width: 15px;
        }
        .news-ticker-item {
            font-size: 12px;
        }
    }
</style>
<script>
(function () {
    var closeBtn = document.getElementById('newsTickerClose');
    var tickerBar = document.getElementById('newsTickerBar');
    var content = document.getElementById('content');
    if (!closeBtn || !tickerBar) return;

    var text = tickerBar.getAttribute('data-text') || '';
    var storageKey = 'news_ticker_closed_' + text.replace(/\s+/g, '_').slice(0, 80);

    try {
        if (sessionStorage.getItem(storageKey) === '1') {
            tickerBar.style.display = 'none';
            return;
        }
    } catch (e) {}

    closeBtn.addEventListener('click', function () {
        tickerBar.style.display = 'none';
        if (content) {
            var currentPt = parseInt(window.getComputedStyle(content).paddingTop, 10) || 0;
            var tickerH = window.innerWidth <= 767 ? 30 : 34;
            content.style.paddingTop = Math.max(0, currentPt - tickerH) + 'px';
        }
        try {
            sessionStorage.setItem(storageKey, '1');
        } catch (e) {}
    });
})();
</script>
@endif
