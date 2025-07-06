@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-globe ml-3"></i> Web 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Retorno de Mídia 
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('noticia/web') }}" class="btn btn-info pull-right mr-3"><i class="fa fa-table"></i> Notícias</a>
                    <a href="{{ url('noticia/web/atualiza-retorno') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-dollar"></i> Atualizar Valores</a>
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
                        <h6>Fontes</h6>
                        <table id="" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Fonte</th>
                                    <th class="text-right">Valor</th>
                                    <th class="center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inconsistencias as $inconsistencia)
                                    <tr>
                                        <td><a title="Editar" href="{{ ($inconsistencia->nome ) ? url('fonte-web/editar', $inconsistencia->id) : '' }}" target="BLANK" class="text-info">{!! $inconsistencia->nome !!}</a></td>
                                        <td class="text-right">{!! $inconsistencia->nu_valor !!}</td>
                                        <td class="center">{!! $inconsistencia->total !!}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>  
                </div>
                <div class="col-lg-8 col-md-8 col-sm-12">
                    @forelse($noticias as $noticia)
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-10">
                                            <h6>
                                                <a href="{{ url('fonte-web/editar/'.$noticia->id_fonte) }}" target="_BLANK">{!! ($noticia->id_fonte) ? $noticia->nome : '<span>Sem Fonte</span>' !!}</a>
                                                {{ ($noticia->data_noticia) ? \Carbon\Carbon::parse($noticia->data_noticia)->format('d/m/Y') : 'Não informada' }} 
                                            </h6>
                                            {!! ($noticia->sinopse) ? Str::limit($noticia->sinopse, 500, " ...") : '<span class="text-danger center">Notícia não possui texto</span>' !!}
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="pull-right">
                                                <span class="badge badge-pill badge-danger">{{ ($noticia->valor_retorno ) ? $noticia->valor_retorno : 'R$ ---' }}</span>
                                                <br/>

                                                <a title="Editar" href="{{ url('noticia/web/'.$noticia->id.'/editar') }}" target="_BLANK" class="btn btn-primary btn-fill btn-icon btn-sm pull-right" style="border-radius: 20px;">
                                                    <i class="fa fa-edit fa-3x text-white"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <span class="text-danger">Nenhuma notícia sem valor de retorno</span>
                    @endforelse         
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