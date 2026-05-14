@extends('layouts.app')
@section('title', __('link_terms') . ' - TubeLift')

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
        <h1>{{ __('link_terms') }}</h1>
        <p style="color:var(--text-muted); font-weight:700; margin-bottom:30px">{{ app()->getLocale() === 'pt' ? 'ÚLTIMA ATUALIZAÇÃO' : 'LAST UPDATED' }}: {{ date('d/m/Y') }}</p>
        
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
        
        <div style="margin-top:40px; border-top:1px solid var(--border); padding-top:30px">
            <a href="/" class="btn-start" style="display:inline-flex; width:auto; text-decoration:none;">
                {{ app()->getLocale() === 'pt' ? 'Voltar para Home' : 'Back to Home' }}
            </a>
        </div>
    </div>
</div>
@endsection
