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
<body style="background: #f7f7f7; font-family: Tahoma, Arial,sans-serif; font-size: 12px; padding-top: 20px; padding-bottom: 20px;">
    <div style="width: 800px; margin: 0 auto; background: white; padding: 10px 20px; margin-top: 30px;">
        <table>
            <tbody>
                <tr>
                    <td>
                        @if($boletim->id_cliente == 452)
                            <img src="https://studiosocial.app/img/logo_offshore.jpg">
                        @else
                            <img src="https://studiosocial.app/img/logo_zurich.png">
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
        <div style="text-align: right;">
            <span>Foram encontradas {{ count($dados) }} notícias</span>
        </div>  
        <div style="text-align: right; margin-top: 5px;">
            <span><a href="{{ url('boletim/'.$boletim->id.'/visualizar') }}">Clique aqui</a> para ver o boletim no navagador</span>
        </div>      

            @php
                $area = "";
                $tipo = "";
                $tipo_formatado = "";
            @endphp
            @foreach($dados as $key => $noticia)
                @if($noticia->area != null)

                    @if($noticia->area != $area)
                        <table style="border-bottom: 1px solid #2196f3; width: 100%; margin-bottom: 8px;">
                            <tr>
                                <td style="width: 30px;">
                                    <img src="https://studiosocial.app/img/icone.jpg">
                                </td>
                                <td style="color: #2196f3; font-size: 20px !important; text-transform: uppercase;">
                                    {{ $noticia->area }}
                                </td>
                            </tr>
                        </table>
                        @php
                            $flag = true;
                        @endphp
                    @endif

                    @if($noticia->clipagem != $tipo or($noticia->clipagem == $tipo and $noticia->area != $area))
                        @switch($noticia->clipagem)
                            @case('web')
                                @php
                                    $tipo_formatado = 'Web';
                                    $icone = 'web';
                                @endphp
                            @break
                            @case('tv')
                                @php
                                    $tipo_formatado = 'TV';
                                    $icone = 'tv';
                                @endphp
                            @break
                            @case('radio')
                                @php
                                    $tipo_formatado = 'Rádio';
                                    $icone = 'radio';
                                @endphp
                            @break
                            @case('jornal')
                                @php
                                    $tipo_formatado = 'Jornal';
                                    $icone = 'jornal';
                                @endphp
                            @break
                            @default
                                @php
                                    $tipo_formatado = 'Clipagens';
                                @endphp
                            @break                                    
                        @endswitch
                        <div style="text-transform: uppercase; font-weight: 600;">
                          <table>
                            <tr>
                              <td><img class="icone" src="https://studiosocial.app/img/icone_{{ $icone }}.png"></td>
                              <td><p>{!! $tipo_formatado !!}</p></td>
                            </tr>
                          </table> 
                        </div>
                    @endif

                    @if($noticia->clipagem == 'tv')
                        
                        <div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 10px; padding-bottom: 10px; line-height: 17px;">
                            <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia->data)) }}</p>
                            <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Emissora:</strong> {{ $noticia->INFO1 }}</p>
                            @if($noticia->INFO2)
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Programa:</strong> {{ $noticia->INFO2 }}</p>
                            @endif
                            <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Duração:</strong> {{ gmdate("H:i:s", $noticia->segundos)}}</p>
                            <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Sinopse:</strong> {!! $sinopse = strip_tags(str_replace('Sinopse 1 - ', '', $noticia->sinopse)) !!}</p>

                            @php 
                                $file_headers = @get_headers(env('FILE_URL').$noticia->clipagem.'/arquivo'.$noticia->id.'_1.mp4');
                            @endphp
                            @if(!(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found'))
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Link:</strong> <a href="{{ env('FILE_URL').$noticia->clipagem.'/arquivo'.$noticia->id.'_1.mp4' }}" download>Assista</a></p>
                            @endif
                        </div>

                    @elseif($noticia->clipagem == 'radio')

                        <div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 10px; padding-bottom: 10px; line-height: 17px;">
                            <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia->data)) }}</p>
                            <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Emissora:</strong> {{ $noticia->INFO1 }}</p>
                            @if($noticia->INFO2)
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Programa:</strong> {{ $noticia->INFO2 }}</p>
                            @endif
                            <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Duração:</strong> {{ gmdate("H:i:s", $noticia->segundos)}}</p>
                            <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Sinopse:</strong> {!! $sinopse = strip_tags(str_replace('Sinopse 1 - ', '', $noticia->sinopse)) !!}</p>

                            @php 
                                $file_headers = @get_headers(env('FILE_URL').$noticia->clipagem.'/arquivo'.$noticia->id.'_1.mp3');
                            @endphp
                            @if(!(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found'))
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Link:</strong> <a href="{{ env('FILE_URL').$noticia->clipagem.'/arquivo'.$noticia->id.'_1.mp3' }}" download>Ouça</a></p>
                            @endif
                        </div>
                    
                    @elseif($noticia->clipagem == 'web')

                        <div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 10px; padding-bottom: 10px; line-height: 17px;">
                            <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Título:</strong> {{ $noticia->titulo }}</p>
                            <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia->data)) }}</p>
                            <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Veículo:</strong> {{ $noticia->INFO1 }}</p>
                            @if($noticia->INFO2)
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Seção:</strong> {{ $noticia->INFO2 }}</p>
                            @endif
                            <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Sinopse:</strong> {!! $sinopse = strip_tags(str_replace('Sinopse 1 - ', '', $noticia->sinopse)) !!}</p>
                            <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Link:</strong><a href="{{ $noticia->link }}" target="_BLANK"> Acesse</a></p>

                            @php 
                                $file_headers = @get_headers($noticia->url);
                            @endphp
                            @if(!(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found'))
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Print:</strong> <a href="{{ $noticia->url }}" target="BLANK" download>Veja</a></p>
                            @endif
                        </div>                            

                    @else

                        <div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 10px; padding-bottom: 10px; line-height: 17px;">
                            <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Título:</strong> {{ $noticia->titulo }}</p>
                            <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia->data)) }}</p>
                            <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Veículo:</strong> {{ $noticia->INFO1 }}</p>
                            @if($noticia->INFO2)
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Seção:</strong> {{ $noticia->INFO2 }}</p>
                            @endif
                            <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Sinopse:</strong> {!! $sinopse = strip_tags(str_replace('Sinopse 1 - ', '', $noticia->sinopse)) !!}</p>

                            @php 
                                $file_headers = @get_headers($noticia->url);
                            @endphp
                            @if(!(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found'))
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Link:</strong> <a href="{{ $noticia->url }}" download>Veja</a></p>
                            @endif
                        </div>
                        
                    @endif

                    @php
                        $area = $noticia->area;
                        $tipo = $noticia->clipagem;
                    @endphp

                @endif
            @endforeach
        
        <div style="width: 50%; margin: 0 auto; text-align: center;">
            <img src="https://studiosocial.app/img/logo_studio.jpg">
        </div>
    </div> 
  </body>
</html>