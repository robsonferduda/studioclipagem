<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
  <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/paper-dashboard.css?v=2.0.1') }}" rel="stylesheet" />
  <title>{{ $boletim->titulo }}</title>
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
                           <img src="{{ asset('img/clientes/logo_expandida/'.$boletim->cliente->logo_expandida) }}">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-md-12">
                        <span class="pull-right mr-3">Total de notícias: {{ count($noticias_impresso) + count($noticias_web) + count($noticias_radio) + count($noticias_tv) }}</span>
                        @if(count($noticias_impresso) > 0)
                            <p style="text-transform: uppercase; font-weight: 600;"><i class="fa fa-newspaper-o"></i> Clipagens de Jornal</p>
                        @endif
                        @foreach($noticias_impresso as $key => $noticia)
                            <div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 10px; padding-bottom: 10px;">
                                <p style="margin-bottom: 0px;"><strong>Título:</strong> {!! ($noticia->titulo) ? : '<span class="text-danger">Notícia sem título</span>' !!}</p>
                                <p style="margin-bottom: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia->dt_clipagem)) }}</p>
                                <p style="margin-bottom: 0px;"><strong>Veículo:</strong> {{ $noticia->fonte->nome }}</p>
                                <p style="margin-bottom: 0px;"><strong>Seção:</strong> {{ ($noticia->secao) ? $noticia->secao->ds_sessao : 'Não informado' }}</p>
                                <p style="margin-bottom: 0px;"><strong>Sinopse:</strong> {!! $sinopse = strip_tags(str_replace('Sinopse 1 - ', '', $noticia->sinopse)) !!}</p>
                                <p style="margin-bottom: 10px;"><strong>Link:</strong> <a href="{{ asset('img/noticia-impressa/'.$noticia->ds_caminho_img) }}" target="_blank">Veja</a></p>
                            </div>
                        @endforeach

                        @if(count($noticias_web) > 0)
                            <p style="text-transform: uppercase; font-weight: 600;"><i class="fa fa-globe"></i> Clipagens de Web</p>
                        @endif
                        @foreach($noticias_web as $key => $noticia)
                            <div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 10px; padding-bottom: 10px;">
                                <p style="margin-bottom: 0px;"><strong>Título:</strong> {!! ($noticia->titulo_noticia) ? : '<span class="text-danger">Notícia sem título</span>' !!}</p>
                                <p style="margin-bottom: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia->data_noticia)) }}</p>
                                <p style="margin-bottom: 0px;"><strong>Veículo:</strong> {{ $noticia->fonte->nome }}</p>
                                <p style="margin-bottom: 0px;"><strong>Seção:</strong> {{ ($noticia->secao) ? $noticia->secao->ds_sessao : 'Não informado' }}</p>
                                <p style="margin-bottom: 0px;"><strong>Sinopse:</strong> {!! $sinopse = strip_tags(str_replace('Sinopse 1 - ', '', $noticia->sinopse)) !!}</p>
                                <p style="margin-bottom: 10px;"><strong>Link:</strong> <a href="{{ asset('img/noticia-web/'.$noticia->ds_caminho_img) }}" target="_blank">Veja</a></p>
                            </div>
                        @endforeach

                        @if(count($noticias_tv) > 0)
                            <p style="text-transform: uppercase; font-weight: 600;"><i class="fa fa-tv"></i> Clipagens de TV</p>
                        @endif
                        @foreach($noticias_tv as $key => $noticia)
                            <div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 10px; padding-bottom: 10px;">
                                <p style="margin-bottom: 0px;"><strong>Título:</strong> {!! ($noticia->titulo) ? : '<span class="text-danger">Notícia sem título</span>' !!}</p>
                                <p style="margin-bottom: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia->dt_noticia)) }}</p>
                                <p style="margin-bottom: 0px;"><strong>Veículo:</strong> {{ $noticia->emissora->nome_emissora }}</p>
                                <p style="margin-bottom: 0px;"><strong>Seção:</strong> {{ ($noticia->secao) ? $noticia->secao->ds_sessao : 'Não informado' }}</p>
                                <p style="margin-bottom: 0px;"><strong>Sinopse:</strong> {!! $sinopse = strip_tags(str_replace('Sinopse 1 - ', '', $noticia->sinopse)) !!}</p>
                                <p style="margin-bottom: 10px;"><strong>Link:</strong> <a href="{{ asset('img/noticia-web/'.$noticia->ds_caminho_img) }}" target="_blank">Veja</a></p>
                            </div>
                        @endforeach


                        @if(count($noticias_radio) > 0)
                            <p style="text-transform: uppercase; font-weight: 600;"><i class="fa fa-volume-up"></i> Clipagens de TV</p>
                        @endif
                        @foreach($noticias_radio as $key => $noticia)
                            <div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 10px; padding-bottom: 10px;">
                                <p style="margin-bottom: 0px;"><strong>Título:</strong> {!! ($noticia->titulo) ? : '<span class="text-danger">Notícia sem título</span>' !!}</p>
                                <p style="margin-bottom: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia->dt_noticia)) }}</p>
                                <p style="margin-bottom: 0px;"><strong>Veículo:</strong> {{ $noticia->emissora->nome_emissora }}</p>
                                <p style="margin-bottom: 0px;"><strong>Seção:</strong> {{ ($noticia->secao) ? $noticia->secao->ds_sessao : 'Não informado' }}</p>
                                <p style="margin-bottom: 0px;"><strong>Sinopse:</strong> {!! $sinopse = strip_tags(str_replace('Sinopse 1 - ', '', $noticia->sinopse)) !!}</p>
                                <p style="margin-bottom: 10px;"><strong>Link:</strong> <a href="{{ asset('img/noticia-web/'.$noticia->ds_caminho_img) }}" target="_blank">Veja</a></p>
                            </div>
                        @endforeach

                    </div>        
                </div>
            </div>
        </div>
    </div>
</body> 