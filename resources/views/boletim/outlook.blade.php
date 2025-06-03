<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Security-Policy" content="script-src 'none'; connect-src 'none'; object-src 'none'; form-action 'none';"> 
    <meta charset="UTF-8"> 
    <meta content="width=device-width, initial-scale=1" name="viewport"> 
    <meta name="x-apple-disable-message-reformatting"> 
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> 
    <meta content="telephone=no" name="format-detection"> 
    <title>Boletim Digital</title> 
    <style>

    </style> 
</head> 
<body style="background: white; font-family: Tahoma, Arial,sans-serif; font-size: 12px; padding-top: 20px; padding-bottom: 20px;">
    <div style="width: 800px; margin: 0 auto; background: white; padding: 10px 20px; margin-top: 30px;">
    <table width="800px;" style="width: 800px; background: white;">
        <tr>
            <td>
                <table width="800px;" style="width: 800px;">
                    <tbody>
                        <tr>
                            <td>
                                <img src="{{ asset('img/clientes/logo_expandida/'.$boletim->cliente->logo_expandida) }}">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div style="text-align: right;">
                    <span style="position: absolute; bottom: 0px; right: 0px;">Total de notícias: {{ count($noticias_impresso) + count($noticias_web) + count($noticias_radio) + count($noticias_tv) }}</span>
                </div>  
                <div style="text-align: right; margin-top: 5px;">
                    <span><a href="{{ url('boletim/'.$boletim->id.'/visualizar') }}">Clique aqui</a> para ver o boletim no navegador</span>
                </div>
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
                                <p style="margin-bottom: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia->dt_noticia)) }}</p>
                                <p style="margin-bottom: 0px;"><strong>Emissora:</strong> {{ $noticia->emissora->nome_emissora }}</p>
                                <p style="margin-bottom: 0px;"><strong>Programa:</strong> {{ ($noticia->programa) ? $noticia->programa->nome_programa : 'Não informado' }}</p>
                                <p style="margin-bottom: 0px;"><strong>Duração:</strong> {{ ($noticia->duracao) ? $noticia->duracao : 'Não informado' }}</p>
                                <p style="margin-bottom: 0px;"><strong>Sinopse:</strong> {!! $sinopse = strip_tags(str_replace('Sinopse 1 - ', '', $noticia->sinopse)) !!}</p>
                                <p style="margin-bottom: 10px;"><strong>Link:</strong> <a href="{{ asset('video/noticia-tv/'.$noticia->ds_caminho_video) }}" target="_blank">Assista</a></p>
                            </div>
                        @endforeach


                        @if(count($noticias_radio) > 0)
                            <p style="text-transform: uppercase; font-weight: 600;"><i class="fa fa-volume-up"></i> Clipagens de Rádio</p>
                        @endif
                        @foreach($noticias_radio as $key => $noticia)
                            <div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 10px; padding-bottom: 10px;">
                                <p style="margin-bottom: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia->dt_clipagem)) }}</p>
                                <p style="margin-bottom: 0px;"><strong>Emissora:</strong> {{ $noticia->emissora->nome_emissora }}</p>
                                <p style="margin-bottom: 0px;"><strong>Programa:</strong> {{ ($noticia->programa) ? $noticia->programa->nome_programa : 'Não informado' }}</p>
                                <p style="margin-bottom: 0px;"><strong>Duração:</strong> {{ ($noticia->duracao) ? $noticia->duracao : 'Não informado' }}</p>
                                <p style="margin-bottom: 0px;"><strong>Sinopse:</strong> {!! $sinopse = strip_tags(str_replace('Sinopse 1 - ', '', $noticia->sinopse)) !!}</p>
                                <p style="margin-bottom: 10px;"><strong>Link:</strong> <a href="{{ asset('audio/noticia-radio/'.$noticia->ds_caminho_audio) }}" target="_blank">Ouça</a></p>
                            </div>
                        @endforeach

            </td>
        </tr>
    </table>
    </div> 
  </body>
</html>