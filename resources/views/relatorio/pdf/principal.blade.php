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

        .no-break {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .clipagem-item {
            page-break-before: always;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .imagem, .header {
            page-break-inside: avoid;
            break-inside: avoid;
        }

    </style>
</head>
<body>
    <!-- Listagem de notícias de jornal -->
    @if(count($dados_impresso) > 0)
        <!-- Nome da Fonte - Seção - Página - Data - Cidade/UF -->
        @foreach($dados_impresso as $key => $noticia)
            
                        <div class="header">
                            <h5 style="text-align: justify; margin-bottom: 0px; padding-bottom: 5px; margin-top: 26px; font-size: 17px; border-bottom: 1px solid black; ">Clipagem de Jornal</h5>    
                            <p style="text-align: justify; font-size: 16px; margin:0px; padding: 0px; margin-top: 8px; margin-bottom: 8px;">
                                {{ $noticia->fonte }}
                                {{ ($noticia->secao) ? " - ".$noticia->secao  : '' }}
                                {{ ($noticia->pagina) ? " - Página: ".$noticia->pagina : '' }}
                                {{ " - ".$noticia->data_formatada }}
                                {{ ($noticia->nm_cidade) ? " - ".trim($noticia->nm_cidade."/".$noticia->sg_estado) : '' }}
                                {{ ($noticia->nm_estado and !$noticia->nm_cidade) ? " - ".trim($noticia->sg_estado) : ''}}
                            </p> 
                        </div>
                        <div style="text-align: center;">
                            <img style="margin: 0 auto;" src="https://studioclipagem.com/img/noticia-impressa/{{ $noticia->midia }}">
                        </div>
            @if($key < count($dados_impresso) -1)
                <div style="page-break-before: always;"></div>                        
            @endif                  
        @endforeach
    @endif

    @if(count($dados_web) > 0)
        <!-- Nome da Fonte - Seção - Página - Data - Cidade/UF -->
        @foreach($dados_web as $key => $noticia)
            <div class="header">
                <h5 style="text-align: justify; margin-bottom: 0px; padding-bottom: 5px; margin-top: 26px; font-size: 17px; border-bottom: 1px solid black; ">Clipagem de Web</h5>    
                    <p style="text-align: justify; font-size: 16px; margin:0px; padding: 0px; margin-top: 8px; margin-bottom: 8px; position: relative;">
                        {{ $noticia->fonte }}
                        {{ " - ".$noticia->data_formatada }}
                        {{ ($noticia->nm_cidade) ? " - ".trim($noticia->nm_cidade."/".$noticia->sg_estado) : '' }}
                        {{ ($noticia->nm_estado and !$noticia->nm_cidade) ? " - ".trim($noticia->sg_estado) : ''}}
                        <img style="width: 15px; height: 15px; position: absolute; right: 0;" src="https://studioclipagem.com/img/globe.png">
                    </p> 
            </div>
            <div style="text-align: center;">
                <img style="margin: 0 auto;" src="https://studioclipagem.com/img/noticia-web/{{ $noticia->midia }}">
            </div>
            @if($key < count($dados_web) -1)
                <div style="page-break-before: always;"></div>                        
            @endif               
        @endforeach
    @endif
</body>
</html>