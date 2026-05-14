@extends('layouts.app')
@section('title', __('link_dmca') . ' - TubeLift')

@section('content')
<style>
    .page-content { padding: 40px 0 80px; line-height: 1.8; color: var(--text-secondary); }
    .page-content h1 { color: var(--text-main); margin-bottom: 20px; font-size: clamp(32px, 5vw, 48px); font-weight: 800; letter-spacing: -1px; }
    .page-content h2 { color: var(--text-main); margin: 40px 0 20px; font-weight: 700; font-size: 24px; }
    .page-content p { margin-bottom: 20px; font-size: 16px; color: var(--text-muted); }
    .legal-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius); padding: 40px; box-shadow: var(--shadow-lg); }
    @media (max-width: 768px) {
        .legal-card { padding: 25px; }
    }
</style>

<div class="page-content">
    <div class="legal-card">
        <h1>{{ __('link_dmca') }}</h1>
        <p style="color:var(--text-muted); font-weight:700; margin-bottom:30px">{{ app()->getLocale() === 'pt' ? 'ÚLTIMA ATUALIZAÇÃO' : 'LAST UPDATED' }}: {{ date('d/m/Y') }}</p>
        
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
        
        <div style="margin-top:40px; border-top:1px solid var(--border); padding-top:30px">
            <a href="/" class="btn-start" style="display:inline-flex; width:auto; text-decoration:none;">
                {{ app()->getLocale() === 'pt' ? 'Voltar para Home' : 'Back to Home' }}
            </a>
        </div>
    </div>
</div>
@endsection
