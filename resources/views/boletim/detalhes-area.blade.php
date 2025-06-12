@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h5 class="card-title ml-2">
                        <i class="fa fa-file-o"></i> Boletim 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> {{ $boletim->titulo }}
                    </h5>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('boletins') }}" class="btn btn-primary pull-right"><i class="fa fa-file-o"></i> Boletins</a>
                    <a href="{{ url('boletim/'.$boletim->id.'/enviar') }}" class="btn btn-success pull-right"><i class="fa fa-send"></i> Verificar e Enviar</a>
                    <a href="{{ url('boletim/'.$boletim->id.'/visualizar') }}" class="btn btn-warning pull-right"><i class="fa fa-eye"></i> Visualizar</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                <div style="text-align: right;">
                    @if(count($dados) > 1)
                        <span>Foram encontradas {{ count($dados) }} notícias</span>
                    @else
                        <span>Foi encontrada {{ count($dados) }} notícia</span>
                    @endif
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
                                <p style="margin-bottom: 10px; margin-top: 0px;"><strong>Print:</strong><a href="{{ asset($noticia['path_midia']) }}" target="_blank"> Veja</a></p>
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
            </div>
        </div>
    </div>
</div> 
@endsection