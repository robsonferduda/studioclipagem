@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title">
                        <i class="fa fa-globe"></i> Jornal Web 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Cadastrar Notícia 
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('fonte-web/listar') }}" class="btn btn-info pull-right mr-3"><i class="fa fa-globe"></i> Fontes Web</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['buscar-web']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data Clipagem</label>
                                        <input type="text" class="form-control datepicker" name="dt_inicial" required="true" value="{{ date("d/m/Y") }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data Noticia</label>
                                        <input type="text" class="form-control datepicker" name="dt_final" required="true" value="{{ date("d/m/Y") }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <div class="form-group">
                                        <label>Categoria</label>
                                        <select class="form-control select2" name="regra" id="regra">
                                            <option value="">Selecione uma categoria</option>
                                            @foreach ($fontes as $fonte)
                                                <option value="{{ $fonte->id }}">{{ $fonte->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Fonte</label>
                                            <select class="form-control select2" name="regra" id="regra">
                                                <option value="">Selecione uma fonte</option>
                                                @foreach ($fontes as $fonte)
                                                    <option value="{{ $fonte->id }}">{{ $fonte->nome }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                </div>
                            </div>    
                            <div class="text-center mb-2 mt-3">
                                <button type="submit" class="btn btn-success" name="btn_enviar" value="salvar"><i class="fa fa-save"></i> Salvar</button>
                                <button type="submit" class="btn btn-warning" name="btn_enviar" value="salvar_e_copiar"><i class="fa fa-copy"></i> Salvar e Copiar</button>
                                <a href="{{ url('noticia/web') }}" class="btn btn-danger"><i class="fa fa-times"></i> Cancelar</a>
                            </div>
                        </div>
                    {!! Form::close() !!} 
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection