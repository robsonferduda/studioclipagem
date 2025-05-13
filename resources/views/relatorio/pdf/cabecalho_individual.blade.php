<div style="clear:both; margin-top: 20px;">
    <div style="width: 80%; float: left;">
        <h5 style="margin-bottom: 0px; padding-bottom: 5px; margin-top: 26px; font-size: 17px; border-bottom: 2px solid #b5b4b4;">{{ $noticia->fonte->nome }}</h5>
        <p style="color: #eb8e06; margin: 0; font-weight: bold;">{{ \Carbon\Carbon::parse($noticia->dt_pub)->format('d/m/Y') }} - {{ ($noticia->fonte) ? $noticia->fonte->nome : '' }}  {{ ($noticia->id_sessao_impresso) ? "- ".$noticia->secao->ds_sessao : '' }}</p>  
        <p style="color: #eb8e06; margin: 0; margin-top: -3px;">{{ ($noticia->cd_estado) ? $noticia->estado->nm_estado : '' }}{{ ($noticia->cd_cidade) ? "/".$noticia->cidade->nm_cidade : '' }}</p>   
        <p style="color: #eb8e06; margin: 0; margin-top: -3px;">
            @if($noticia->nu_pagina_atual)
                Página {{ $noticia->nu_pagina_atual }}
            @else
                <span class="text-danger">Página não informada</span>
            @endif
        </p>   
    </div>
    <div style="width: 20%; float: right; text-align: right; padding: 10px;">
        <img style="width: 100%" src="{{ public_path('img/logo.png') }}"/>
    </div>
</div> 
<div style="clear:both">
    
</div>