@extends('layouts.app')
@section('content')
<div class="col-md-12">
    {!! Form::open(['id' => 'frm_user_create', 'url' => ['perfis']]) !!}
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="card-title ml-2">
                            <i class="nc-icon nc-circle-10"></i> Usuários 
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i> Perfil
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i> Novo
                        </h4>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ url('perfis') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-table"></i> Perfis</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        @include('layouts.mensagens')
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nome <span class="text-danger">Obrigatório</span></label>
                            <input type="text" class="form-control" name="name" id="name" placeholder="Nome" value="{{ old('name') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Chave <span class="text-danger">Obrigatório</span></label>
                            <input type="text" class="form-control" name="display_name" id="display_name" placeholder="Chave" value="{{ old('display_name') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Cor <span class="text-danger">Obrigatório, informar código hexadecimal</span></label>
                            <input type="text" class="form-control" name="display_color" id="display_color" placeholder="Cor" value="{{ old('display_color') }}">
                        </div>
                    </div>
                </div>  
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control" name="description" id="description" rows="3"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center mb-2">
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Salvar</button>
                <a href="{{ url('usuarios') }}" class="btn btn-danger"><i class="fa fa-times"></i> Cancelar</a>
            </div>
        </div>
    {!! Form::close() !!} 
</div> 
@endsection