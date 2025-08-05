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
                @if(count($dados) > 1)
                    <span>Foram encontradas {{ count($dados) }} notícias</span>
                @else
                    <span>Foi encontrada {{ count($dados) }} notícia</span>
                @endif
            </div>  
            <div style="text-align: right; margin-top: 5px;">
                <span><a href="{{ url('boletim/'.$boletim->id.'/visualizar') }}">Clique aqui</a> para ver o boletim no navegador</span>
            </div>      

                @php
                    $area = "";
                    $tipo = "";
                    $tipo_formatado = "";
                @endphp
                @foreach($dados as $key => $noticia)

                        @if($noticia['area'] != $area)
                            <table style="border-bottom: 1px solid #2196f3; width: 100%; margin-bottom: 8px;">
                                <tr>
                                    <td style="width: 30px;">
                                        <img src="https://studiosocial.app/img/icone.jpg">
                                    </td>
                                    <td style="color: #2196f3; font-size: 20px !important; text-transform: uppercase;">
                                        {{ $noticia['area'] }}
                                    </td>
                                </tr>
                            </table>
                            @php
                                $flag = true;
                            @endphp
                        @endif

                        @if($noticia['tipo'] != $tipo or($noticia['tipo'] == $tipo and $noticia['area'] != $area))
                            @switch($noticia['tipo'])
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
                                @case('impresso')
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

                        @if($noticia['tipo'] == 'tv')
                            
                            <div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 10px; padding-bottom: 10px; line-height: 17px;">
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia['data_noticia'])) }}</p>
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Emissora:</strong> {{ $noticia['fonte'] }}</p>
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Programa:</strong> {{ $noticia['programa'] }}</p>
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Duração:</strong> {{ $noticia['duracao'] }}</p>
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Sinopse:</strong> {!! $noticia['sinopse'] !!}</p>
                                <p style="margin-bottom: 10px; margin-top: 0px;"><strong>Link:</strong> <a href="{{ asset($noticia['path_midia']) }}" target="_blank">Assista</a></p>
                            </div>

                        @elseif($noticia['tipo'] == 'radio')

                            <div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 10px; padding-bottom: 10px; line-height: 17px;">
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia['data_noticia'])) }}</p>
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Emissora:</strong> {{ $noticia['fonte'] }}</p>
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Programa:</strong> {{ $noticia['programa'] }}</p>
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Duração:</strong> {{ $noticia['duracao'] }}</p>
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Sinopse:</strong> {!! $noticia['sinopse'] !!}</p>
                                <p style="margin-bottom: 10px; margin-top: 0px;"><strong>Link:</strong> <a href="{{ asset($noticia['path_midia']) }}" target="_blank">Ouça</a></p>
                            </div>
                        
                        @elseif($noticia['tipo'] == 'web')

                            <div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 10px; padding-bottom: 10px; line-height: 17px;">
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Título:</strong> {!! ($noticia['titulo']) ? : '<span class="text-danger">Notícia sem título</span>' !!}</p>
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia['data_noticia'])) }}</p>
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Veículo:</strong> {{ $noticia['fonte'] }}</p>
                                @if($noticia['secao'])
                                    <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Seção:</strong> {{ ($noticia['secao']) ? $noticia['secao'] : 'Não informado' }}</p>
                                @endif
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Sinopse:</strong> {!! $noticia['sinopse'] !!}</p>
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Link:</strong><a href="{{ $noticia['url_noticia'] }}" target="_blank"> Acesse</a></p>
                                @if($dados->fl_print)
                                    <p style="margin-bottom: 10px; margin-top: 0px;"><strong>Print:</strong><a href="{{ asset($noticia['path_midia']) }}" target="_blank"> Veja</a></p>
                                @endif
                            </div>                            

                        @else

                            <div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 10px; padding-bottom: 10px; line-height: 17px;">
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Título:</strong> {!! ($noticia['titulo']) ? : '<span class="text-danger">Notícia sem título</span>' !!}</p>
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Data:</strong> {{ date('d/m/Y', strtotime($noticia['data_noticia'])) }}</p>
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Veículo:</strong> {{ $noticia['fonte'] }}</p>
                                @if($noticia['secao'])
                                    <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Seção:</strong> {{ ($noticia['secao']) ? $noticia['secao'] : 'Não informado' }}</p>
                                @endif
                                <p style="margin-bottom: 0px; margin-top: 0px;"><strong>Sinopse:</strong> {!! $noticia['sinopse'] !!}</p>
                                <p style="margin-bottom: 10px; margin-top: 0px;"><strong>Link:</strong> <a href="{{ asset($noticia['path_midia']) }}" target="_blank">Veja</a></p>
                            </div>
                            
                        @endif

                        @php
                            $area = $noticia['area'];
                            $tipo = $noticia['tipo'];
                        @endphp
                @endforeach
            </td>
        </tr>
    </table>
    </div> 
  </body>
</html>