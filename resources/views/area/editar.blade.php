@extends('layouts.app')
@section('content')
<div class="col-md-12">
    {!! Form::open(['id' => 'frm_area_cadastrar', 'url' => ['areas/' . $area->id . '/atualizar'],  'method' => 'post']) !!}
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="card-title ml-3"><i class="fa fa-tags"></i> Áreas
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Editar</h4>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ url('areas') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-table"></i> Áreas</a>
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
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Descrição <span class="text-danger">Obrigatório</span></label>
                            <input type="text" class="form-control" name="descricao" id="descricao" placeholder="Descrição" value="{{ $area->descricao }}" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center mb-3">
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Salvar</button>
                <a href="{{ url('areas') }}" class="btn btn-danger"><i class="fa fa-times"></i> Cancelar</a>
            </div>
        </div>
    {!! Form::close() !!}
</div>
@endsection
