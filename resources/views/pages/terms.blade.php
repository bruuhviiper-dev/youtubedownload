<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('link_terms') }} - TubeLift</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="stylesheet" href="/css/app.css">
    <style>
        .page-content { padding: 60px 0 120px; line-height: 1.8; color: var(--text-secondary); }
        .page-content h1 { color: white; margin-bottom: 40px; font-size: clamp(32px, 5vw, 52px); font-weight: 900; letter-spacing: -2px; }
        .page-content h2 { color: var(--accent); margin: 50px 0 25px; font-weight: 800; font-size: 24px; }
        .page-content p { margin-bottom: 20px; font-size: 17px; }
        .legal-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 30px; padding: 60px; box-shadow: 0 40px 100px rgba(0,0,0,0.5); }
    </style>
</head>
<body>
    <div class="container">
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
        
        <div class="page-content">
            <div class="legal-card">
                <h1>{{ __('link_terms') }}</h1>
                <p style="color:var(--text-muted); font-weight:800; margin-bottom:40px">{{ app()->getLocale() === 'pt' ? 'ÚLTIMA ATUALIZAÇÃO' : 'LAST UPDATED' }}: {{ date('d/m/Y') }}</p>
                
                @if(app()->getLocale() === 'pt')
                    <h2>1. Aceitação dos Termos</h2>
                    <p>Ao acessar e usar o TubeLift, você concorda em cumprir estes termos de serviço e todas as leis e regulamentos aplicáveis. Se você não concordar com algum destes termos, está proibido de usar este site.</p>
                    
                    <h2>2. Licença de Uso</h2>
                    <p>O TubeLift é destinado exclusivamente para uso pessoal e arquivamento privado de mídia. Você é o único responsável pelo conteúdo que baixa e deve garantir que possui o direito legal de fazê-lo.</p>
                    
                    <h2>3. Isenção de Responsabilidade</h2>
                    <p>Os materiais no TubeLift são fornecidos 'como estão'. Não oferecemos garantias, expressas ou implícitas, e por este meio isentamos todas as outras garantias, incluindo, sem limitação, condições de comercialização ou adequação a um fim específico.</p>
                @else
                    <h2>1. Acceptance of Terms</h2>
                    <p>By accessing and using TubeLift, you agree to comply with these terms of service and all applicable laws and regulations. If you do not agree with any of these terms, you are prohibited from using this site.</p>
                    
                    <h2>2. Use License</h2>
                    <p>TubeLift is intended exclusively for personal use and private media archiving. You are solely responsible for the content you download and must ensure that you have the legal right to do so.</p>
                    
                    <h2>3. Disclaimer</h2>
                    <p>The materials on TubeLift are provided 'as is'. We make no warranties, expressed or implied, and hereby disclaim all other warranties including, without limitation, conditions of merchantability or fitness for a particular purpose.</p>
                @endif
                
                <div style="margin-top:60px; border-top:1px solid var(--border); padding-top:40px">
                    <a href="/" class="btn-start" style="display:inline-block; text-decoration:none; height:50px; line-height:50px; padding:0 40px; font-size:14px">
                        {{ app()->getLocale() === 'pt' ? 'Voltar para Home' : 'Back to Home' }}
                    </a>
                </div>
            </div>
        </div>

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
</body>
</html>
