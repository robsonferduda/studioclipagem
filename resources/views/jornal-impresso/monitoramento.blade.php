@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-newspaper-o"></i> Jornal Impresso 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Monitoramento 
                    </h4>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary pull-right mr-3"><i class="fa fa-plus"></i> Novo</button>
                    <a href="{{ url('monitoramento/executar') }}" class="btn btn-warning pull-right mr-3"><i class="fa fa-bolt"></i> Executar</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['buscar-monitoramento']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Mostrar Notícias Vinculadas</label>
                                        <select class="form-control select2" name="cliente" id="cliente">
                                            <option value="">Selecione um cliente</option>
                                            @foreach ($clientes as $cliente)
                                                <option value="{{ $cliente->id }}">{{ $cliente->pessoa->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>     
                        </div>
                    {!! Form::close() !!}
                </div>               
                @foreach($noticias as $noticia)
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card card-stats ml-3 mr-3" style="border: 1px solid #f1f1f1;">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-md-12">
                                
                                        <p>
                                            <h6>
                                                {{ $noticia->cliente->pessoa->nome }} 
                                            </h6>
                                        </p>
                                        <p class="card-title">
                                            {{ $noticia->noticiaImpressa->titulo }}
                                        </p>
                                        <p class="text-bold">
                                            {!! \Illuminate\Support\Str::limit($noticia->noticiaImpressa->texto, 300, '...') !!}
                                        </p>                                    
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer ">
                                <hr>
                                <div class="row">
                                    <div class="col-6 col-md-6">
                                        <div class="stats">
                                            <i class="fa fa-calendar"></i>Vinculo registrado em {{ \Carbon\Carbon::parse($noticia->created_at)->format('d/m/Y H:i:s') }}
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-6">
                                        <div class="pull-right">
                                            <a href="{{ url('noticia-impressa/cliente/'.$noticia->cliente_id.'/editar', $noticia->noticiaImpressa->id) }}" class="btn btn-primary btn-round btn-icon btn-sm">
                                                <i class="fa fa-edit fa-2x"></i>
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
@endsection