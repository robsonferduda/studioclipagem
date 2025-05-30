<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório</title>
    <style>
        .page-break {
            page-break-after: always; /* Força uma quebra de página depois */
        }

        .header {
            margin-bottom: 5mm; /* Espaço entre o cabeçalho e a imagem */
        }

        /* Estilos gerais */
        body {
            font-family: Arial, sans-serif;
            margin: 2px;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;            
        }

        img{
            max-width: 100%;
            height: auto;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Listagem de notícias de jornal -->
    @if($tipo == 'impresso')
        <div class="header">
            <h5 style="text-align: justify; margin-bottom: 0px; padding-bottom: 5px; margin-top: 26px; font-size: 17px; border-bottom: 1px solid black;">Clipagem de Jornal</h5>    
            <p style="text-align: justify; font-size: 16px; margin:0px; padding: 0px; margin-top: 8px; margin-bottom: 8px;">
                {{ $noticia->fonte->nome }}
                {{ ($noticia->secao) ? " - ".$noticia->secao->ds_sessao  : '' }}
                {{ ($noticia->nu_pagina_atual) ? " - Página: ".$noticia->nu_pagina_atual : '' }}
                {{ " - ".\Carbon\Carbon::parse($noticia->dt_pub)->format('d/m/Y') }}
                {{ ($noticia->cidade) ? " - ".trim($noticia->cidade->nm_cidade."/".$noticia->estado->sg_estado) : '' }}
                {{ ($noticia->estado and !$noticia->cidade) ? " - ".trim($noticia->estado->sg_estado) : ''}}
            </p> 
            <div style="text-align: center;">
                @if($noticia->ds_caminho_img)
                    <img style="margin: 0 auto;" src="{{ public_path('img/noticia-impressa/'.$noticia->ds_caminho_img) }}">
                @else
                    <img style="margin: 0 auto;" src="{{ public_path('img/no-print.png') }}">
                @endif
            </div>
        </div>
    @endif

    @if($tipo == 'web')
        <div class="header">
            <h5 style="text-align: justify; margin-bottom: 0px; padding-bottom: 5px; margin-top: 26px; font-size: 17px; border-bottom: 1px solid black;">Clipagem de Web</h5>   
            <p style="text-align: justify; font-size: 16px; margin:0px; padding: 0px; margin-top: 8px; margin-bottom: 8px; position: relative;">
                {{ $noticia->fonte->nome }}
                {{ ($noticia->secao) ? " - ".$noticia->secao->ds_sessao  : '' }}
                {{ " - ".\Carbon\Carbon::parse($noticia->data_noticia)->format('d/m/Y') }}
                {{ ($noticia->cidade) ? " - ".trim($noticia->cidade->nm_cidade."/".$noticia->estado->sg_estado) : '' }}
                {{ ($noticia->estado and !$noticia->cidade) ? " - ".trim($noticia->estado->sg_estado) : ''}}
                <a href="{{ $noticia->url_noticia }}" style="position: absolute; right: 0px;" 
                   target="_BLANK">
                    <img style="width: 20px; height: 20px;" src="{{ public_path('img/globe.png') }}">
                </a>
            </p>  
            <div style="text-align: center;">
                @if($noticia->ds_caminho_img)
                    <img style="margin: 0 auto;" src="{{ public_path('img/noticia-web/'.$noticia->ds_caminho_img) }}">
                @else
                    <img style="margin: 0 auto;" src="{{ public_path('img/no-print.png') }}">
                @endif
            </div>       
        </div>                  
    @endif
</body>
</html>
    
