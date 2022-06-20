<div class="row">
    <div class="col-lg-12 col-md-12">
        @if(Session::get('flag_regras'))
            <div class="alert alert-info alert-with-icon alert-dismissible fade show" data-notify="container">
                <span data-notify="icon" class="nc-icon nc-bell-55"></span>
                <span data-notify="message"><b>Lembrete Importante!</b> Os totais e listagens de postagens estão considerando somente mensagens filtradas por alguma regra.</span>
            </div>
        @else
            <div class="alert alert-danger alert-with-icon alert-dismissible fade show" data-notify="container">
                <span data-notify="icon" class="nc-icon nc-bell-55"></span>
                <span data-notify="message"><b>Lembrete Importante!</b> Os totais e listagens de postagens estão considerando todas as mensagens, ignorando a aplicação de qualquer regra.</span>
            </div>
        @endif
    </div>
</div>