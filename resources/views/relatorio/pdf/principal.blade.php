<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $nome }}</title>
    <style>
        .page-break {
            page-break-after: always; /* Força uma quebra de página depois */
        }

        .header {
            font-weight: bold;
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
            max-width: 100%; /* Garante que a imagem caiba na largura da página */
            height: auto;   /* Mantém a proporção da imagem */
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
                                {{ " - Página: ".$noticia->pagina }}
                                {{ " - ".$noticia->data_formatada." - " }}
                                {{ ($noticia->nm_cidade) ? trim($noticia->nm_cidade."/".$noticia->sg_estado) : '' }}
                                {{ ($noticia->nm_estado and !$noticia->nm_cidade) ? trim($noticia->sg_estado) : ''}}
                            </p>  
                        </div>
                        
                        @foreach ($partesDaImagem as $parte)
                            <img src="{{ asset($parte) }}" alt="Parte da Imagem">
                        @endforeach  

                        @if($key < count($dados_impresso) -2)
                            <div style="page-break-after: always;"></div>
                        @endif
                  
        @endforeach
    @endif

    @if(false)
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
                            <img src="{{ Storage::disk('s3')->temporaryUrl($noticia->midia, '+30 minutes') }}"/>
                        @endif   
                    </td>
                </tr>
            </table>
              
        @endforeach
    @endif
</body>
</html>
    
