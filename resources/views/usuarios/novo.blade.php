@extends('layouts.app')
@section('content')
<div class="col-md-12">
    {!! Form::open(['id' => 'frm_user_create', 'url' => ['usuario']]) !!}
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="card-title ml-2"><i class="nc-icon nc-circle-10"></i> Usuários > Novo</h4>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ url('usuarios') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-table"></i> Usuários</a>
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
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nome <span class="text-danger">Obrigatório</span></label>
                            <input type="text" class="form-control" name="name" id="name" placeholder="Nome" value="{{ old('name') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email <span class="text-danger">Obrigatório</span></label>
                            <input type="text" class="form-control" name="email" id="email" placeholder="Email" value="{{ old('email') }}">
                        </div>
                    </div>
                </div>  
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Senha <span class="text-danger">Obrigatório</span></label>
                            <input type="password" class="form-control" name="password" id="password" value="{{ old('password') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Confirmação de Senha <span class="text-danger">Obrigatório</span></label>
                            <input type="password" class="form-control" name="confirm_password" id="confirm_password" value="{{ old('confirm_password') }}">
                        </div>
                    </div>
                </div>  
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check mt-3">
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" {{ ( old('is_active')) ? 'checked' : '' }} type="checkbox" name="is_active" value="true">
                                    CADASTRO ATIVO
                                    <span class="form-check-sign"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>          
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Salvar</button>
                <a href="{{ url('usuarios') }}" class="btn btn-danger"><i class="fa fa-times"></i> Cancelar</a>
            </div>
        </div>
    {!! Form::close() !!} 
</div> 
@endsection