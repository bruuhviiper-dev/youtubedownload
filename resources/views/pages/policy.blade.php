@extends('layouts.app')
@section('title', __('link_privacy') . ' - TubeLift')

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
        <h1>{{ __('link_privacy') }}</h1>
        <p style="color:var(--text-muted); font-weight:700; margin-bottom:30px">{{ app()->getLocale() === 'pt' ? 'ÚLTIMA ATUALIZAÇÃO' : 'LAST UPDATED' }}: {{ date('d/m/Y') }}</p>
        
        @if(app()->getLocale() === 'pt')
            <h2>1. Informações que Coletamos</h2>
            <p>O TubeLift valoriza sua privacidade acima de tudo. Nosso serviço não exige a criação de contas ou o fornecimento de dados pessoais. Não coletamos nomes, e-mails ou endereços IP de forma identificável.</p>
            
            <h2>2. Cookies e Rastreamento</h2>
            <p>Utilizamos cookies técnicos essenciais para o funcionamento do site e para melhorar sua experiência. Parceiros de publicidade podem utilizar cookies anônimos para exibir anúncios relevantes.</p>
            
            <h2>3. Segurança dos Dados</h2>
            <p>Todos os processos de download são temporários. Os arquivos são removidos automaticamente de nossos servidores após 24 horas, garantindo que nada permaneça armazenado permanentemente.</p>
        @else
            <h2>1. Information We Collect</h2>
            <p>TubeLift values your privacy above all. Our service does not require the creation of accounts or the provision of personal data. We do not collect names, emails, or IP addresses in an identifiable manner.</p>
            
            <h2>2. Cookies and Tracking</h2>
            <p>We use essential technical cookies for the website to function and to improve your experience. Advertising partners may use anonymous cookies to display relevant ads.</p>
            
            <h2>3. Data Security</h2>
            <p>All download processes are temporary. Files are automatically removed from our servers after 24 hours, ensuring that nothing remains permanently stored.</p>
        @endif
        
        <div style="margin-top:40px; border-top:1px solid var(--border); padding-top:30px">
            <a href="/" class="btn-start" style="display:inline-flex; width:auto; text-decoration:none;">
                {{ app()->getLocale() === 'pt' ? 'Voltar para Home' : 'Back to Home' }}
            </a>
        </div>
    </div>
</div>
@endsection
