@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title ml-2">
                        <i class="fa fa-file-o"></i> Boletins
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Cadastrados 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> {{ \Carbon\Carbon::parse(Session::get('data_atual'))->format('d/m/Y') }}
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
                    <div class="form-group m-3 w-70">
                        <div class="row">
                            <div class="col-md-12">
                                <p class="text-info">São listados todos os boletins gerados na data atual. Para selecionar outro período, utilize as opções na tela.</p>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="font-black"><i class="fa fa-filter"></i> Filtrar por cliente</label>
                                    <select class="form-control select2" name="regra" id="regra">
                                        <option value="">Selecione um cliente</option>
                                        @foreach ($clientes as $cliente)
                                            <option value="{{ $cliente->id }}">{{ $cliente->pessoa->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Data Inicial</label>
                                    <input type="text" class="form-control datepicker" name="dt_final" required="true" value="{{ date("d/m/Y") }}" placeholder="__/__/____">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Data Final</label>
                                    <input type="text" class="form-control datepicker" name="dt_final" required="true" value="{{ date("d/m/Y") }}" placeholder="__/__/____">
                                </div>
                            </div>
                        </div>     
                    </div>
                </div>   
            </div>     
        </div>
    </div>
</div> 
@endsection