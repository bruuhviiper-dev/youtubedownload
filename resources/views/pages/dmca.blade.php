<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('link_dmca') }} - TubeLift</title>
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
                <h1>{{ __('link_dmca') }}</h1>
                <p style="color:var(--text-muted); font-weight:800; margin-bottom:40px">{{ app()->getLocale() === 'pt' ? 'ÚLTIMA ATUALIZAÇÃO' : 'LAST UPDATED' }}: {{ date('d/m/Y') }}</p>
                
                @if(app()->getLocale() === 'pt')
                    <h2>Política de Direitos Autorais</h2>
                    <p>O TubeLift respeita a propriedade intelectual de terceiros. Como provedor de serviços técnicos, não hospedamos vídeos ou áudios em nossos servidores de forma permanente.</p>
                    
                    <h2>Notificação de Infração</h2>
                    <p>Se você acredita que seu trabalho foi utilizado de forma a constituir infração de direitos autorais, envie uma notificação para nossa equipe com as informações necessárias para análise.</p>
                    
                    <h2>Ação de Conformidade</h2>
                    <p>Responderemos prontamente a avisos de suposta infração que cumpram com a Lei de Direitos Autorais do Milênio Digital (DMCA) e outras leis de propriedade intelectual aplicáveis.</p>
                @else
                    <h2>Copyright Policy</h2>
                    <p>TubeLift respects the intellectual property of others. As a technical service provider, we do not host videos or audio on our servers permanently.</p>
                    
                    <h2>Notice of Infringement</h2>
                    <p>If you believe that your work has been used in a way that constitutes copyright infringement, please send a notice to our team with the necessary information for review.</p>
                    
                    <h2>Compliance Action</h2>
                    <p>We will respond promptly to notices of alleged infringement that comply with the Digital Millennium Copyright Act (DMCA) and other applicable intellectual property laws.</p>
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
