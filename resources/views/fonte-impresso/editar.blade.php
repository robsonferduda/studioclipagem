@extends('layouts.app')
@section('style')
<style>
    .top-40 {
        margin-top: 40px!important;
    }
</style>
@endsection
@section('content')
<div class="col-md-12">
    {!! Form::open(['id' => 'frm_jornal_impresso_editar', 'url' => ['fonte-impresso/'. $fonte->id. '/atualizar'], 'method' => 'post']) !!}
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="card-title ml-3"><i class="fa fa-newspaper-o"></i> Jornal Impresso
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Atualizar</h4>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('fonte-impresso/listar') }}" class="btn btn-primary pull-right mr-3"><i class="fa fa-table"></i> Jornal Impresso</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        @include('layouts.mensagens')
                    </div>
                </div>
                <div class="row mr-1 ml-1">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Código</label>
                            <input type="text" class="form-control" name="codigo" id="codigo" placeholder="Código" value="{{ $fonte->codigo }}">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Tipo <span class="text-danger">Obrigatório</span></label>
                            <select class="form-control" name="tipo" id="tipo">
                                <option value="">Selecione um tipo</option>
                                <option value="1" {{ ($fonte->tipo == 1) ? 'selected' : '' }}>Jornal</option>
                                <option value="2" {{ ($fonte->tipo == 2) ? 'selected' : '' }}>Revista</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Coleta <span class="text-danger">Obrigatório</span></label>
                            <select class="form-control" name="coleta" id="coleta">
                                <option value="">Selecione a coleta</option>
                                <option value="1" {{ ($fonte->coleta == 2) ? 'selected' : '' }}> Automática</option>
                                <option value="2" {{ ($fonte->coleta == 2) ? 'selected' : '' }}>Upload</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Nome <span class="text-danger">Obrigatório</span></label>
                            <input type="text" class="form-control" name="nome" id="nome" placeholder="Nome" required value="{{ $fonte->nome }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>URL de Coleta <span class="text-danger">Obrigatório</span></label>
                            <input type="text" class="form-control" name="url" id="url" placeholder="Nome" required value="{{ $fonte->url }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Estado {{ $fonte->cd_estado }}</label>
                            <select class="form-control" name="cd_estado" id="cd_estado">
                                <option value="">Selecione</option>
                                @foreach ($estados as $estado)
                                    <option value="{{ $estado->cd_estado }}" {!! $fonte->cd_estado == $estado->cd_estado ? " selected" : '' !!}>{{ $estado->nm_estado }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Cidade </label>
                            <input type="hidden" name="cd_cidade" id="cd_cidade" value="{{ $fonte->cd_cidade }}">
                            <select class="form-control select2" name="cidade" id="cidade" disabled="disabled">
                                <option value="">Selecione uma cidade</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-check mt-3">
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" {{ ($fonte->with_login) ? 'checked' : '' }} type="checkbox" name="with_login" value="true">
                                        Exige Login
                                    <span class="form-check-sign"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center mb-2">
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Salvar</button>
                <a href="{{ url('fonte-impresso/listar') }}" class="btn btn-danger ml-2"><i class="fa fa-times"></i> Cancelar</a>
            </div>
        </div>
    {!! Form::close() !!}
</div>
@endsection
@section('script')
    <script src="{{ asset('js/cropper-main.js') }}"></script>
    <script>
        $(document).ready(function(){
            $("#cd_estado").trigger("change");
        });
    </script>
@endsection
