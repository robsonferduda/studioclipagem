@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-volume-up ml-3"></i> Rádio 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Retorno de Mídia 
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('radios') }}" class="btn btn-info pull-right mr-3"><i class="fa fa-table"></i> Notícias</a>
                    <a href="{{ url('radio/noticias/atualizar-valores') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-dollar"></i> Atualizar Valores</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row ml-1 mr-1">
                <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="card card-stats">
                            <div class="card-body ">
                                <div class="row">
                                    <div class="col-5 col-md-4">
                                        <div class="icon-big text-center icon-warning">
                                        <i class="fa fa-exclamation-circle text-warning"></i>
                                        </div>
                                    </div>
                                    <div class="col-7 col-md-8">
                                        <div class="numbers">
                                        <p class="card-category">Pendentes</p>
                                        <p class="card-title">{{ $total_nulos }}</p>
                                        <p></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer ">
                                <hr>
                                <div class="stats">
                                    <i class="fa fa-exclamation-circle"></i>
                                    Notícias sem valor de retorno
                                </div>
                            </div>
                        </div>
                        <h6>Emissoras</h6>
                        <table id="" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Emissora</th>
                                    <th class="text-right">Valor</th>
                                    <th class="center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inconsistencias as $inconsistencia)
                                    <tr>
                                        <td><a title="Editar" href="{{ route('emissora.edit', $inconsistencia->id) }}" target="BLANK" class="text-info">{!! $inconsistencia->nome_emissora !!}</a></td>
                                        <td class="text-right">{!! $inconsistencia->nu_valor !!}</td>
                                        <td class="center">{!! $inconsistencia->total !!}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table> 

                        <h6>Programas</h6>
                        <table id="" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Programa</th>
                                    <th class="text-right">Valor</th>
                                    <th class="center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($programas as $inconsistencia)
                                    <tr>
                                        <td><a title="Editar" href="{{ url('tv/emissoras/programas/editar', $inconsistencia->id) }}" target="BLANK" class="text-info">{!! $inconsistencia->nome_programa !!}</a></td>
                                        <td class="text-right">{!! $inconsistencia->valor_segundo !!}</td>
                                        <td class="center">{!! $inconsistencia->total !!}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>        
                    </div>
                <div class="col-lg-8 col-md-8 col-sm-12">
                    @foreach($noticias as $noticia)
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-10">
                                            <h6>
                                                <a href="{{ url('emissora/'.$noticia->emissora_id.'/edit') }}" target="_BLANK">{{ ($noticia->emissora_id) ? $noticia->nome_emissora : '' }}</a>
                                                - {{ ($noticia->dt_clipagem) ? \Carbon\Carbon::parse($noticia->dt_clipagem)->format('d/m/Y') : 'Não informada' }} 
                                            </h6>
                                            <p class="mb-1">
                                                @if($noticia->duracao)
                                                    Duração <strong>{{ $noticia->duracao }}</strong></strong>
                                                @else
                                                    <span class="text-danger">Duração não informada</span>
                                                @endif
                                            </p>  
                                            {!! ($noticia->sinopse) ? Str::limit($noticia->sinopse, 500, " ...") : '<span class="text-danger center">Notícia não possui texto</span>' !!}
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="pull-right">
                                                <span class="badge badge-pill badge-danger">{{ ($noticia->valor_retorno ) ? $noticia->valor_retorno : 'R$ ---' }}</span>
                                                <br/>

                                                <a title="Editar" href="{{ url('noticia-radio/'.$noticia->id.'/editar') }}" target="_BLANK" class="btn btn-primary btn-fill btn-icon btn-sm pull-right" style="border-radius: 20px;">
                                                    <i class="fa fa-edit fa-3x text-white"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach         
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection
@section('script')
    <script>
        $(document).ready(function() { 

            var host =  $('meta[name="base-url"]').attr('content');


        });
    </script>
@endsection