<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
  <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/paper-dashboard.css?v=2.0.1') }}" rel="stylesheet" />
  <style>
      .corpo_boletim{
        background: #f7f7f7;
        padding-bottom: 25px;
      }
      .wrapper{
          width: 800px;
          margin: 60px auto;
          margin-bottom: 30px;
      }
    </style>
</head>
<body class="corpo_boletim">
    <div class="wrapper">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-12">
                           <img src="{{ asset('img/clientes/logo_expandida/'.$boletim->cliente->logo) }}">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-md-12">
                        <span class="pull-right mr-3">Total de notícias: {{ count($noticias_impresso) }}</span>
                        @if(count($noticias_impresso) > 0)
                            <p style="text-transform: uppercase; font-weight: 600;"><i class="fa fa-newspaper-o"></i> Clipagens de Jornal</p>
                        @endif
                        @foreach($noticias_impresso as $key => $noticia)
                            <div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 10px; padding-bottom: 10px;">
                                <p style="margin-bottom: 0px;"><strong>Título:</strong> {!! ($noticia->titulo) ? : '<span class="text-danger">Notícia sem título</span>' !!}</p>
                                <p style="margin-bottom: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia->dt_clipagem)) }}</p>
                                <p style="margin-bottom: 0px;"><strong>Veículo:</strong> {{ $noticia->fonte->nome }}</p>
                                <p style="margin-bottom: 0px;"><strong>Seção:</strong> {{ $noticia->INFO2 }}</p>
                                <p style="margin-bottom: 0px;"><strong>Sinopse:</strong> {!! $sinopse = strip_tags(str_replace('Sinopse 1 - ', '', $noticia->sinopse)) !!}</p>
                                <p style="margin-bottom: 10px;"><strong>Link:</strong> <a href="{{ asset('img/noticia-impressa/'.$noticia->ds_caminho_img) }}" download>Download</a></p>
                            </div>
                        @endforeach
                    </div>        
                </div>
            </div>
        </div>
    </div>
</body> 