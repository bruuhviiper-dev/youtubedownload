<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ __('subtitle') }}">
    <title>{{ __('title') }}</title>
    
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    <!-- SEO Hreflang Tags -->
    <link rel="alternate" hreflang="x-default" href="{{ route('home') }}" />
    <link rel="alternate" hreflang="pt" href="{{ route('home.locale', ['locale' => 'pt']) }}" />
    <link rel="alternate" hreflang="en" href="{{ route('home.locale', ['locale' => 'en']) }}" />
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/css/app.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

<div class="container">
    <!-- Navbar -->
    <nav class="navbar">
        <a href="/" class="logo">
            <svg class="logo-svg" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="100" height="100" rx="25" fill="url(#cyber_grad)"/>
                <path d="M50 30V65M50 65L35 50M50 65L65 50" stroke="white" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M30 75H70" stroke="white" stroke-width="8" stroke-linecap="round"/>
                <defs>
                    <linearGradient id="cyber_grad" x1="0" y1="0" x2="100" y2="100" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#B00759"/>
                        <stop offset="1" stop-color="#7e0540"/>
                    </linearGradient>
                </defs>
            </svg>
            <span class="logo-text">Tube<span>Lift</span></span>
        </a>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <h1>{{ __('title') }}</h1>
        <p>{{ __('subtitle') }}</p>

        <div class="search-wrapper">
            <div class="search-card">
                <input type="url" class="search-input" id="urlInput" placeholder="{{ __('input_placeholder') }}" required autocomplete="off">
                <button class="btn-start" id="btnParse" onclick="handleParse(event)">
                    <span id="btnParseText">{{ __('analyze_button') }}</span>
                    <span id="btnParseSpinner" class="spinner hidden"></span>
                </button>
            </div>
            <div id="errorBox" class="error-msg hidden" style="margin-top:25px; color:var(--accent); font-weight:900; font-size:18px"></div>
        </div>

        <!-- History Section -->
        <div id="historySection" class="history-section hidden">
            <div class="history-title">{{ app()->getLocale() === 'pt' ? 'Downloads Recentes' : 'Recent Downloads' }}</div>
            <div id="historyGrid" class="history-grid"></div>
        </div>
    </section>

    <!-- Result Card -->
    <div id="resultCard" class="result-container hidden">
        <div class="video-header">
            <img id="videoThumb" class="video-thumb-large" src="" alt="Thumb">
            <div class="video-meta">
                <h2 id="videoTitle"></h2>
                <div id="videoViews" style="margin-bottom:20px; color:var(--accent); font-size:16px; font-weight:900"></div>
                
                <div class="format-tabs">
                    <button class="format-tab active" data-type="video" onclick="switchTab('video')">{{ __('tab_video') }}</button>
                    <button class="format-tab" data-type="audio" onclick="switchTab('audio')">{{ __('tab_audio') }}</button>
                </div>
            </div>
        </div>
        
        <div id="formatList"></div>

        <div class="security-banner">
            <div class="security-info">
                <div class="security-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div>
                    <div class="security-title">Segurança Cibernética</div>
                    <div class="security-desc">Processamento anônimo e sem rastreadores.</div>
                </div>
            </div>
            <button class="btn-start btn-small">SAIBA MAIS</button>
        </div>
    </div>

    <!-- Progress UI -->
    <div id="progressCard" class="result-container card-padded hidden">
        <h3 id="progressTitle" style="margin-bottom:40px; font-size:24px; font-weight:900; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"></h3>
        
        <div class="progress-container">
            <div class="progress-track">
                <div class="progress-fill" id="progressFill" style="width: 0%"></div>
            </div>
        </div>

        <div style="display:flex; justify-content: space-between; font-size:16px; font-weight:900; color:var(--text-secondary); margin-top:20px">
            <span id="progressStatus">{{ __('status_pending') }}</span>
            <span id="progressPercent">0%</span>
        </div>
    </div>

    <!-- Complete UI -->
    <div id="completeCard" class="result-container complete-card hidden">
        <div class="complete-icon-wrapper">
            <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="4"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <h2 class="complete-title">{{ __('status_completed') }}</h2>
        <p id="completeInfo" class="complete-info"></p>
        <a class="btn-start btn-large" id="btnSave" href="#" download>{{ __('button_download') }}</a>
        <br><br>
        <button class="btn-reset" onclick="resetAll()">NOVO DOWNLOAD</button>
    </div>

    <!-- Features Section -->
    <section class="features">
        <h2 style="font-size:48px; margin-bottom:30px; font-weight:900; letter-spacing:-2px">{{ __('seo_title') }}</h2>
        <p style="color:var(--text-secondary); max-width:700px; font-size:22px; margin-bottom:60px">{{ __('seo_p1') }}</p>
        
        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <svg width="45" height="45" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h4>Privacidade Extrema</h4>
                <p>Processamento anônimo e seguro. Seus dados nunca são armazenados ou compartilhados.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <svg width="45" height="45" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
                <h4>Potência 4K</h4>
                <p>{{ __('seo_p2') }}</p>
            </div>
        </div>
    </section>

    <!-- FAQ Accordion -->
    <section class="faq-section">
        <h2 style="text-align:center; font-size:52px; font-weight:900; margin-bottom:80px; letter-spacing:-3px">{{ __('faq_title') }}</h2>
        <div class="accordion">
            <div class="accordion-item">
                <div class="accordion-header" onclick="toggleAccordion(this)">
                    <span>{{ __('faq_1_q') }}</span>
                    <div class="accordion-icon"></div>
                </div>
                <div class="accordion-content">{{ __('faq_1_a') }}</div>
            </div>
            <div class="accordion-item">
                <div class="accordion-header" onclick="toggleAccordion(this)">
                    <span>{{ __('faq_2_q') }}</span>
                    <div class="accordion-icon"></div>
                </div>
                <div class="accordion-content">{{ __('faq_2_a') }}</div>
            </div>
            <div class="accordion-item">
                <div class="accordion-header" onclick="toggleAccordion(this)">
                    <span>{{ __('faq_3_q') }}</span>
                    <div class="accordion-icon"></div>
                </div>
                <div class="accordion-content">{{ __('faq_3_a') }}</div>
            </div>
            <div class="accordion-item">
                <div class="accordion-header" onclick="toggleAccordion(this)">
                    <span>{{ __('faq_4_q') }}</span>
                    <div class="accordion-icon"></div>
                </div>
                <div class="accordion-content">{{ __('faq_4_a') }}</div>
            </div>
        </div>
    </section>

    <!-- Luxury Footer -->
    <footer>
        <div class="footer-logo">Tube<span>Lift</span></div>
        <div class="footer-text">{{ __('footer_copyright') }}</div>
        <div class="footer-links">
            <a href="{{ route('policy') }}">{{ __('link_privacy') }}</a>
            <a href="{{ route('terms') }}">{{ __('link_terms') }}</a>
            <a href="{{ route('dmca') }}">{{ __('link_dmca') }}</a>
        </div>
    </footer>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const LANG = {
    error_invalid: "{{ __('error_invalid_url') }}",
    error_connection: "{{ __('error_connection') }}",
    error_process: "{{ __('error_process') }}",
    status_pending: "{{ __('status_pending') }}",
    status_processing: "{{ __('status_processing') }}",
    status_completed: "{{ __('status_completed') }}",
    status_failed: "{{ __('status_failed') }}",
    button_download: "{{ __('button_download') }}",
    views_text: "{{ app()->getLocale() === 'en' ? 'views' : 'visualizações' }}"
};

let videoData = null;
let formatsData = [];
let pollTimer = null;

document.addEventListener('DOMContentLoaded', () => { loadHistory(); });

function toggleAccordion(header) {
    const item = header.parentElement;
    const isActive = item.classList.contains('active');
    document.querySelectorAll('.accordion-item').forEach(i => i.classList.remove('active'));
    if (!isActive) item.classList.add('active');
}

async function handleParse(e) {
    if(e) e.preventDefault();
    const url = document.getElementById('urlInput').value.trim();
    if (!url) return;

    showParseLoading(true);
    hideError();
    hide('resultCard'); hide('progressCard'); hide('completeCard');

    try {
        const res = await fetch('/api/parse', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ url })
        });
        const data = await res.json();

        if (!data.success) {
            showError(data.message || LANG.error_process);
            return;
        }

        videoData = data.video;
        formatsData = data.formats;
        renderResult();
    } catch (err) {
        showError(LANG.error_connection);
    } finally {
        showParseLoading(false);
    }
}

function renderResult() {
    document.getElementById('videoThumb').src = videoData.thumbnail || '';
    document.getElementById('videoTitle').textContent = videoData.title;
    document.getElementById('videoViews').textContent = formatViews(videoData.view_count) + ' ' + LANG.views_text;
    switchTab('video');
    show('resultCard');
    document.getElementById('resultCard').scrollIntoView({ behavior: 'smooth' });
}

function switchTab(type) {
    document.querySelectorAll('.format-tab').forEach(t => t.classList.toggle('active', t.dataset.type === type));
    const container = document.getElementById('formatList');
    const filtered = formatsData.filter(f => f.type === type);

    if (filtered.length === 0) {
        container.innerHTML = `<div style="text-align:center;padding:80px;color:var(--text-muted);font-weight:900;font-size:20px">Nenhum formato disponível</div>`;
        return;
    }

    container.innerHTML = filtered.map(f => `
        <div class="format-row">
            <div class="format-info-group">
                <div class="quality-badge">${f.quality}</div>
                <div class="format-meta">
                    <div class="format-ext">.${f.ext}</div>
                    <div class="format-size">${f.filesize ? formatBytes(f.filesize) : 'Variado'}</div>
                </div>
            </div>
            <button class="btn-start btn-format" onclick="startDownload('${f.format_id}', '${f.quality}', '${f.type}', '${f.label}')">
                ${LANG.button_download}
            </button>
        </div>
    `).join('');
}

async function startDownload(formatId, quality, type, label) {
    hide('resultCard');
    show('progressCard');
    document.getElementById('progressTitle').textContent = videoData.title;
    document.getElementById('progressPercent').textContent = '0%';
    document.getElementById('progressFill').style.width = '0%';
    
    try {
        const res = await fetch('/api/download', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({
                video_id: videoData.video_id,
                title: videoData.title,
                thumbnail: videoData.thumbnail,
                duration: videoData.duration,
                format_id: formatId,
                quality: quality,
                type: type,
            })
        });
        const data = await res.json();

        if (!data.success) {
            showError(data.message || LANG.status_failed);
            hide('progressCard');
            show('resultCard');
            return;
        }

        startPolling(data.download_id);
    } catch (err) {
        showError(LANG.error_connection);
        hide('progressCard');
        show('resultCard');
    }
}

function startPolling(id) {
    if (pollTimer) clearInterval(pollTimer);
    pollTimer = setInterval(() => checkStatus(id), 2000);
    checkStatus(id);
}

async function checkStatus(id) {
    try {
        const res = await fetch(`/api/status/${id}`);
        const data = await res.json();

        document.getElementById('progressPercent').textContent = data.progress + '%';
        document.getElementById('progressFill').style.width = data.progress + '%';

        const statusMap = {
            pending: LANG.status_pending,
            processing: LANG.status_processing,
            completed: LANG.status_completed,
            failed: LANG.status_failed
        };
        document.getElementById('progressStatus').textContent = statusMap[data.status] || data.status;

        if (data.status === 'completed') {
            clearInterval(pollTimer);
            pollTimer = null;
            addToHistory(data);
            setTimeout(() => {
                hide('progressCard');
                document.getElementById('completeInfo').textContent = `${data.title} • ${data.quality} • ${data.file_size}`;
                document.getElementById('btnSave').href = `/api/file/${id}`;
                show('completeCard');
            }, 600);
        } else if (data.status === 'failed') {
            clearInterval(pollTimer);
            pollTimer = null;
            hide('progressCard');
            showError(data.error_message || LANG.status_failed);
            show('resultCard');
        }
    } catch (err) { }
}

function addToHistory(item) {
    let history = JSON.parse(localStorage.getItem('tubelift_history') || '[]');
    history = history.filter(h => h.download_id !== item.download_id);
    
    // Crucial: ensure thumbnail is saved
    history.unshift({
        download_id: item.download_id,
        title: item.title,
        quality: item.quality,
        thumbnail: item.thumbnail || (videoData ? videoData.thumbnail : ''),
        timestamp: Date.now()
    });
    
    if (history.length > 6) history.pop();
    localStorage.setItem('tubelift_history', JSON.stringify(history));
    loadHistory();
}

function loadHistory() {
    let history = JSON.parse(localStorage.getItem('tubelift_history') || '[]');
    
    // Clean up corrupted items from previous bug
    const validHistory = history.filter(item => item.download_id && item.download_id !== 'undefined');
    if (validHistory.length !== history.length) {
        localStorage.setItem('tubelift_history', JSON.stringify(validHistory));
        history = validHistory;
    }

    const container = document.getElementById('historySection');
    const grid = document.getElementById('historyGrid');

    if (history.length === 0) {
        container.classList.add('hidden');
        return;
    }

    container.classList.remove('hidden');
    grid.innerHTML = history.map(item => `
        <div class="history-card" onclick="window.location.href='/api/file/${item.download_id}'">
            <img src="${item.thumbnail}" class="history-thumb" onerror="this.src='/favicon.svg'" alt="Thumb">
            <div class="history-info">
                <div class="history-name">${item.title}</div>
                <div class="history-meta">${item.quality}</div>
            </div>
            <div class="btn-history-get">Download</div>
        </div>
    `).join('');
}

function resetAll() {
    if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
    document.getElementById('urlInput').value = '';
    hide('resultCard'); hide('progressCard'); hide('completeCard');
    hideError();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function show(id) { document.getElementById(id).classList.remove('hidden'); }
function hide(id) { document.getElementById(id).classList.add('hidden'); }
function showError(msg) { const el = document.getElementById('errorBox'); el.textContent = msg; el.classList.remove('hidden'); }
function hideError() { document.getElementById('errorBox').classList.add('hidden'); }
function showParseLoading(on) {
    document.getElementById('btnParseText').classList.toggle('hidden', on);
    document.getElementById('btnParseSpinner').classList.toggle('hidden', !on);
    document.getElementById('btnParse').disabled = on;
}
function formatViews(n) {
    if (!n) return '0';
    if (n >= 1e6) return (n/1e6).toFixed(1) + 'M';
    if (n >= 1e3) return (n/1e3).toFixed(1) + 'K';
    return n.toString();
}
function formatBytes(b) {
    if (!b) return '—';
    const u = ['B','KB','MB','GB']; let i = 0;
    while (b >= 1024 && i < u.length - 1) { b /= 1024; i++; }
    return b.toFixed(1) + ' ' + u[i];
}
</script>
</body>
</html>
