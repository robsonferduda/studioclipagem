@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title ml-2">
                        <i class="fa fa-file-o"></i> Boletins
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Listar 
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('boletim/cadastrar') }}" class="btn btn-primary pull-right mr-3"><i class="fa fa-plus"></i> Cadastrar Boletim</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-md-12">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['boletins']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-12 mt-0 mb-0">
                                    <p class="text-info">São listados todos os boletins gerados na data atual. Para selecionar outro período, utilize as opções na tela.</p>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-black"><i class="fa fa-filter"></i> Filtrar por cliente</label>
                                        <select class="form-control select2" name="cliente" id="cliente">
                                            <option value="">Selecione um cliente</option>
                                            @foreach ($clientes as $cliente)
                                                <option value="{{ $cliente->id }}" {{ ($cliente_selecionado == $cliente->id) ? 'selected' : '' }}>{{ $cliente->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Data Inicial</label>
                                        <input type="text" class="form-control datepicker" name="dt_inicial" required="true" value="{{ \Carbon\Carbon::parse($dt_inicial)->format('d/m/Y') }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Data Final</label>
                                        <input type="text" class="form-control datepicker" name="dt_final" required="true" value="{{ \Carbon\Carbon::parse($dt_final)->format('d/m/Y') }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary w-100 mt-4" name="btn_enviar" value="salvar"><i class="fa fa-search"></i> Buscar</button>
                                    </div>
                                </div>
                            </div>     
                        </div>
                    {!! Form::close() !!} 
                </div>   
            </div> 
            <div class="col-lg-12 col-md-3 mb-12">
                @forelse($boletins as $key => $boletim)
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="float-left">
                                        <i class="fa fa-file-pdf-o text-danger mr-3 mt-4" aria-hidden="true" style="font-size: 35px;"></i>
                                    </div>
                                    <div class="float-left">
                                        <p class="mb-1"><strong>{{ $boletim->titulo }}</strong></p>
                                        <h6 class="mb-1" style="color: #FF5722;">{{ $boletim->cliente->nome }}</h6>
                                        <p>
                                            Cadastrado em {{ \Carbon\Carbon::parse($boletim->created_at)->format('d/m/Y H:i:s') }}
                                            {!! ($boletim->usuario) ? "por <strong>".$boletim->usuario->name."</strong>" : '' !!}
                                            @if($boletim->total_views == 0)
                                                - Nenhuma visualização
                                            @elseif($boletim->total_views == 1)
                                                - Visualizado {{ $boletim->total_views }} vez
                                            @else($boletim->total_views > 1)
                                                - Visualizado {{ $boletim->total_views }} vezes
                                            @endif
                                        </p>
                                    </div>
                     
                                    <div class="pull-right" style="text-align: right;">
                                        <span class="badge badge-pill badge-{{ $boletim->situacao->ds_color }} mb-0">{{ $boletim->situacao->ds_situacao }}</span>                        
                                        <div>
                                            <a title="Visualizar" href="{{ url('boletim/'.$boletim->id.'/visualizar') }}" class="btn btn-warning btn-link btn-icon pull-right"><i class="fa fa-eye fa-2x"></i></a> 
                                            <a title="Detalhes" href="{{ url('boletim/'.$boletim->id.'/detalhes') }}" class="btn btn-info btn-link btn-icon pull-right"><i class="fa fa-list fa-2x"></i></a> 
                                            <a title="Editar" href="{{ url('boletim/editar/'.$boletim->id) }}" class="btn btn-primary btn-link btn-icon pull-right"><i class="fa fa-edit fa-2x"></i></a>   
                                            <a title="Excluir" href="{{ url('boletim/excluir/'.$boletim->id) }}" class="btn btn-danger btn-link btn-icon btn-excluir pull-right"><i class="fa fa-trash fa-2x"></i></a>                         
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="row">
                        <div class="col-lg-12 mt-0">
                            <p class="text-danger">Nenhum boletim corresponde aos dados selecionados</p>
                        </div>
                    </div>
                @endforelse
            </div>    
        </div>
    </div>
</div> 
@endsection