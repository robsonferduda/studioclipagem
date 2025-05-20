@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row ml-1">
                <div class="col-md-6">
                    <h4 class="card-title">
                        <i class="fa fa-globe"></i> Web 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Notícias 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Coletas 
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('noticia/web/dashboard') }}" class="btn btn-warning pull-right mr-3"><i class="nc-icon nc-chart-pie-36"></i> Dashboard</a>
                    <a href="{{ url('noticia/web/novo') }}" class="btn btn-primary pull-right mr-3"><i class="fa fa-newspaper-o"></i> Novo</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row mb-0">
                <div class="col-lg-12 col-sm-12 mb-0 mt-0">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['noticia/web']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Tipo de Data</label>
                                        <select class="form-control select2" name="tipo_data" id="tipo_data">
                                            <option value="data_insert" {{ ($tipo_data == "data_insert") ? 'selected' : '' }}>Data de Cadastro</option>
                                            <option value="data_noticia" {{ ($tipo_data == "data_noticia") ? 'selected' : '' }}>Data do Clipping</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data Inicial</label>
                                        <input type="text" class="form-control datepicker" name="dt_inicial" required="true" value="{{ \Carbon\Carbon::parse($dt_inicial)->format('d/m/Y') }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data Final</label>
                                        <input type="text" class="form-control datepicker" name="dt_final" required="true" value="{{ \Carbon\Carbon::parse($dt_final)->format('d/m/Y') }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Cliente</label>
                                        <select class="form-control select2" name="cliente" id="cliente">
                                            <option value="">Selecione um cliente</option>
                                            @foreach ($clientes as $cliente)
                                                <option value="{{ $cliente->id }}" {{ ($cliente_selecionado == $cliente->id) ? 'selected' : '' }}>{{ $cliente->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Buscar por <span class="text-primary">Digite o termo ou expressão de busca</span></label>
                                        <input type="text" class="form-control" name="termo" id="termo" minlength="3" placeholder="Termo" value="{{ $termo }}">
                                    </div>
                                </div>
                                <div class="col-md-12 checkbox-radios mb-0">
                                    <button type="submit" id="btn-find" class="btn btn-primary mb-3"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div>
                <div class="col-lg-12 col-sm-12 conteudo">      
                    @if(count($dados))
                        <h6 class="px-3">Mostrando {{ $dados->count() }} de {{ $dados->total() }} notícias</h6> 
                        
                        {{ $dados->onEachSide(1)->appends(['dt_inicial' => \Carbon\Carbon::parse($dt_inicial)->format('d/m/Y'), 
                                                            'dt_final' => \Carbon\Carbon::parse($dt_final)->format('d/m/Y'),
                                                            'termo' => $termo])
                                                            ->links('vendor.pagination.bootstrap-4') }}
                    @endif
                </div>
                <div class="col-lg-12">
                    @if(count($dados) > 0)
                        @foreach ($dados as $key => $noticia)
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12">                                        
                                            <div class="conteudo-noticia mb-1">
                                                <p class="font-weight-bold mb-1">{{ $noticia->titulo_noticia }}</p>
                                                <p class="text-muted mb-1"> 
                                                    {!! ($noticia->data_noticia) ? date('d/m/Y', strtotime($noticia->data_noticia)) : date('d/m/Y', strtotime($noticia->data_noticia)) !!} - 
                                                    {{ $noticia->fonte->nome }}
                                                </p> 
                                                <div>
                                                    {!! ($noticia->conteudo) ?  Str::limit($noticia->conteudo->conteudo, 700, " ...")  : '<span class="text-danger">Nenhum conteúdo coletado</span>' !!}
                                                </div>
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                <hr>
                                    <div class="stats">
                                        <i class="fa fa-refresh"></i>Notícia cadastrada em {{ \Carbon\Carbon::parse($noticia->created_at)->format('d/m/Y H:i:s') }}
                                        <div class="pull-right">
                                            <a title="Excluir" href="{{ url('noticia/web/'.$noticia->id.'/excluir') }}" class="btn btn-danger btn-fill btn-icon btn-sm btn-excluir" style="border-radius: 30px;">
                                                <i class="fa fa-times fa-3x text-white"></i>
                                            </a>
                                            <a title="Editar" href="{{ url('noticia/web/'.$noticia->id.'/editar') }}" class="btn btn-primary btn-fill btn-icon btn-sm" style="border-radius: 30px;">
                                                <i class="fa fa-edit fa-3x text-white"></i>
                                            </a>
                                            <a title="Visualizar" href="{{ url('noticia/web/'.$noticia->id.'/ver') }}" class="btn btn-warning btn-fill btn-icon btn-sm" style="border-radius: 30px;"><i class="fa fa-link fa-3x text-white"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="col-lg-12 col-sm-12 conteudo">      
                    @if(count($dados))
                    {{ $dados->onEachSide(1)->appends(['dt_inicial' => \Carbon\Carbon::parse($dt_inicial)->format('d/m/Y'), 
                                                        'dt_final' => \Carbon\Carbon::parse($dt_final)->format('d/m/Y'),
                                                        'termo' => $termo])
                                                        ->links('vendor.pagination.bootstrap-4') }}
                    @endif
                </div>
            </div>
            <div class="row mt-0">
                <div class="col col-sm-12 col-md-12 col-lg-12">
                    <div class="load-busca" style="min-height: 200px;" >
                        <h6 class="label-resultado ml-3">Resultados da Busca</h6>
                        <div class="resultados m-3"></div>
                    </div>
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
            var token = $('meta[name="csrf-token"]').attr('content');

        });
    </script>
@endsection