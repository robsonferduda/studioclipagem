<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8" />
        <link href="{{ public_path('demo/demo.css') }}" rel="stylesheet" />
        <style>
            *{margin:0;padding:0}

            @page {
                header: page-header;
                footer: page-footer;
            }
            page-footer{color: red; min-height: 100px;}
            #text-footer{font-size: 12px; text-align: center; padding-bottom: 15px;}
    
            .page-break {
                page-break-after: always;
            }

            .info {
                page-break-inside: avoid !important;
            }

            .info > *:first-child {
                page-break-before: avoid !important;
            }

            .info > *:last-child {
                page-break-after: avoid !important;
            }

            .info{
                padding: 8px;
            }

            .borda {
                border-bottom: 1px solid gray;
            }
            .image-container {
                position: relative;
                display: inline-block;
                z-index: 1000;
            }
            .hidden-text {
                position: absolute;
                top: 0;
                left: 0;
                color: white; /* Torna o texto invisível */
                z-index: -1000;
                pointer-events: none; /* Impede interação com o texto */

            }
        </style>
    </head>
    <body style="margin: 0px; padding: 0px;">
        <div>
            <img style="width: 100%" src="{{ public_path('img/relatorios/capa.png') }}"/>
        </div>
        <div class="content" style="margin: 20px; padding: 10px;"> 
            @yield('content')          
        </div>
        <script type="text/php">
            if (isset($pdf)) {
                $text = "Página {PAGE_NUM} / {PAGE_COUNT}";
                $size = 10;
                $font = $fontMetrics->getFont("Verdana");
                $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
                $x = ($pdf->get_width() - $width) / 2;
                $x = ($pdf->get_width() - $width - 5);
                $y = $pdf->get_height() - 30;
                $pdf->page_text($x, $y, $text, $font, $size);
            }
        </script>
    </body>
</html>