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
    @if(count($dados_impresso) > 0)
        <!-- Nome da Fonte - Seção - Página - Data - Cidade/UF -->
        @foreach($dados_impresso as $key => $noticia)
            
                        <div class="header">
                            <h5 style="text-align: justify; margin-bottom: 0px; padding-bottom: 5px; margin-top: 26px; font-size: 17px; border-bottom: 1px solid black;">Clipagem de Jornal</h5>
                            <p style="text-align: justify; color: #eb8e06; margin: 0; margin-bottom: 5px; margin-top: 3px;"><strong>Período: {{ $dt_inicial_formatada }} à {{ $dt_final_formatada }}</strong></p>
                            <p style="text-align: justify; color: #eb8e06; margin: 0; margin-top: -3px;">{{ $noticia->cliente }}</p>      
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
                            <img style="margin: 0 auto;" src="https://studioclipagem.com/img/noticia-impressa/1748101443.jpeg">
                        </div>
            @if($key < count($dados_impresso) -1)
                <div style="page-break-before: always;"></div>                        
            @endif                  
        @endforeach
    @endif

    @if(count($dados_web) > 4555550)
        <!-- Nome da Fonte - Seção - Página - Data - Cidade/UF -->
        @foreach($dados_web as $key => $noticia)
            <table>
                <tr>
                    <td style="text-align: center;">
                        <div class="header">
                            <h5 style="text-align: justify; margin-bottom: 0px; padding-bottom: 5px; margin-top: 26px; font-size: 17px; border-bottom: 1px solid black;">Clipagem de Web</h5>
                            <p style="text-align: justify; color: #eb8e06; margin: 0; margin-bottom: 5px; margin-top: 3px;"><strong>Período: {{ $dt_inicial_formatada }} à {{ $dt_final_formatada }}</strong></p>
                            <p style="text-align: justify; color: #eb8e06; margin: 0; margin-top: -3px;">{{ $noticia->cliente }}</p>      
                            <p style="text-align: justify; font-size: 16px; margin:0px; padding: 0px; margin-top: 8px; margin-bottom: 8px;">
                                {{ $noticia->fonte }}
                                {{ ($noticia->secao) ? " - ".$noticia->secao  : '' }}
                                {{ " - ".$noticia->data_formatada." - " }}
                                {{ ($noticia->nm_cidade) ? trim($noticia->nm_cidade."/".$noticia->sg_estado) : '' }}
                                {{ ($noticia->nm_estado and !$noticia->nm_cidade) ? trim($noticia->sg_estado) : ''}}
                            </p>  
                        </div>
                        @if($noticia->tipo_midia == 'imagem')
                            
                        @endif   
                    </td>
                </tr>
            </table>
              
        @endforeach
    @endif
</body>
</html>
    
