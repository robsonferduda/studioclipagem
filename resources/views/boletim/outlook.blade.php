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
                    @foreach($noticias_impresso as $key => $noticia)
                        <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Título:</strong> {!! ($noticia['titulo']) ? : '<span class="text-danger">Notícia sem título</span>' !!}</p>
                        <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia['data_noticia'])) }}</p>
                        <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Veículo:</strong> {{ $noticia['fonte'] }}</p>
                        @if($noticia['secao'])
                            <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Seção:</strong> {{ ($noticia['secao']) ? $noticia['secao'] : 'Não informado' }}</p>
                        @endif
                        <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Sinopse:</strong> {!! $noticia['sinopse'] !!}</p>
                        <p style="margin-bottom: 10px; margin-top: 0px;"><strong>Link:</strong> <a href="{{ asset($noticia['path_midia']) }}" target="_blank">Veja</a></p>
                    @endforeach
                @endif

                @if(count($noticias_web) > 0)
                    <p style="text-transform: uppercase; font-weight: 600;"><i class="fa fa-globe"></i> Clipagens de Web</p>
                    @foreach($noticias_web as $key => $noticia)
                            <div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 10px; padding-bottom: 10px;">
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Título:</strong> {!! ($noticia['titulo']) ? : '<span class="text-danger">Notícia sem título</span>' !!}</p>
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia['data_noticia'])) }}</p>
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Veículo:</strong> {{ $noticia['fonte'] }}</p>
                                @if($noticia['secao'])
                                    <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Seção:</strong> {{ ($noticia['secao']) ? $noticia['secao'] : 'Não informado' }}</p>
                                @endif
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Sinopse:</strong> {!! $noticia['sinopse'] !!}</p>
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Link:</strong><a href="{{ $noticia['url_noticia'] }}" target="_blank"> Acesse</a></p>
                                <p style="margin-bottom: 10px; margin-top: 0px;"><strong>Print:</strong><a href="{{ asset($noticia['path_midia']) }}" target="_blank"> Veja</a></p>
                            </div>
                    @endforeach
                @endif

                @if(count($noticias_tv) > 0)
                    <p style="text-transform: uppercase; font-weight: 600;"><i class="fa fa-tv"></i> Clipagens de TV</p>
                @endif
                @foreach($noticias_tv as $key => $noticia)
                    <div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 10px; padding-bottom: 10px;">
                        <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia['data_noticia'])) }}</p>
                        <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Emissora:</strong> {{ $noticia['fonte'] }}</p>
                        <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Programa:</strong> {{ $noticia['programa'] }}</p>
                        <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Duração:</strong> {{ $noticia['duracao'] }}</p>
                        <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Sinopse:</strong> {!! $noticia['sinopse'] !!}</p>
                        <p style="margin-bottom: 10px; margin-top: 0px;"><strong>Link:</strong> <a href="{{ asset($noticia['path_midia']) }}" target="_blank">Ouça</a></p>
                    </div>
                @endforeach

                @if(count($noticias_radio) > 0)
                    <p style="text-transform: uppercase; font-weight: 600;"><i class="fa fa-volume-up"></i> Clipagens de Rádio</p>
                @endif
                @foreach($noticias_radio as $key => $noticia)
                    <div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 10px; padding-bottom: 10px;">
                        <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia['data_noticia'])) }}</p>
                        <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Emissora:</strong> {{ $noticia['fonte'] }}</p>
                        <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Programa:</strong> {{ $noticia['programa'] }}</p>
                        <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Duração:</strong> {{ $noticia['duracao'] }}</p>
                        <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Sinopse:</strong> {!! $noticia['sinopse'] !!}</p>
                        <p style="margin-bottom: 10px; margin-top: 0px;"><strong>Link:</strong> <a href="{{ asset($noticia['path_midia']) }}" target="_blank">Ouça</a></p>
                    </div>
                @endforeach
            </td>
        </tr>
    </table>
    <table width="800px;" style="width: 800px; background: white;">
        <tr>
            <td>
                <h6>📧 INFORMAÇÕES SOBRE PRIVACIDADE</h6>
                <p>Este email foi enviado por Studio Clipagem em conformidade com nossa Política de Privacidade.</p> 
                <p>Para saber como coletamos, usamos e protegemos seus dados: <a href="https://studioclipagem.com/politica-de-privacidade">https://studioclipagem.com/politica-de-privacidade</a></p>

                <h6>📍 Studio Clipagem</h6>
                <p>Rua Bento Gonçalves, 183 - Sala 602, Centro - Florianópolis/SC</p>
                <p>📞 (48) 3223-0590 | ✉️ contato@studioclipagem.com.br</p>
            </td>
        </tr>
    </table>
    </div> 
  </body>
</html>