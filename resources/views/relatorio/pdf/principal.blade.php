<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $nome }}</title>
    <style>
        .page-break {
            page-break-after: always; /* Força uma quebra de página antes */
        }

        .image-container {
            page-break-inside: avoid;
        }
        /* Estilos gerais */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }

        h1 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #000;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
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
    @if(count($dados_impresso) > 0)
        <!-- Nome da Fonte - Seção - Página - Data - Cidade/UF -->
        @foreach($dados_impresso as $key => $noticia)
            <div class="image-container" style="background: white;">
            <div style="width: 100%;">
                <h5 style="margin-bottom: 0px; padding-bottom: 5px; margin-top: 26px; font-size: 17px; border-bottom: 1px solid black;">Clipagem de Jornal</h5>
                <p style="color: #eb8e06; margin: 0; margin-bottom: 5px; margin-top: 3px;"><strong>Período: {{ $dt_inicial_formatada }} à {{ $dt_final_formatada }}</strong></p>
                <p style="color: #eb8e06; margin: 0; margin-top: -3px;">{{ $noticia->cliente }}</p>        
            </div>
                <p style="font-size: 16px; margin:0px; padding: 0px; text-align: justify; margin-top: 8px; margin-bottom: 8px;">
                    {{ $noticia->fonte }}
                    {{ ($noticia->secao) ? " - ".$noticia->secao  : '' }}
                    {{ " - Página: ".$noticia->pagina }}
                    {{ " - ".$noticia->data_formatada." - " }}
                    {{ ($noticia->nm_cidade) ? trim($noticia->nm_cidade."/".$noticia->sg_estado) : '' }}
                    {{ ($noticia->nm_estado and !$noticia->nm_cidade) ? trim($noticia->sg_estado) : ''}}
                </p>
                @if($noticia->tipo_midia == 'imagem')
                    <img style="width: 100%;" src="{{ asset('img/noticia-impressa/'.$noticia->midia) }}"/>
                @endif
            </div>  
            @if($key < count($dados_impresso) -1) 
                <div style="page-break-after: always;"></div>   
            @endif    
        @endforeach
    @endif
</body>
</html>
    
