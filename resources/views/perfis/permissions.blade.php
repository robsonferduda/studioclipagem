@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title">
                        <i class="fa fa-group"></i> Perfis
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Permissões 
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('perfis') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-group"></i> Perfis</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="col-md-12">
                <span class="mb-5"><i class="fa fa-check"></i> Marque as permissões que deseja adicionar e desmarque as que deseja remover</span>
                <div class="mt-3" style="font-size: 16px;">
                    {!! Form::open(['id' => 'basic-form', 'url' => ['role/permission', $role->id]]) !!}
                                                        
                        @foreach($permissions as $key => $p)               
                            <label class="fancy-checkbox parsley-success">
                                <input type="checkbox" name="permission[]" value="{{ $p->id }}" {{ (in_array($p->id, old('permission', [])) || isset($role) && $role->permissions->contains($p->id)) ? 'checked' : '' }}>
                                <span style="color: #484848;"> {{ $p->display_name }} ({{ $p->name }})</span>
                            </label><br/>          
                        @endforeach

                        <div class="mt-2">
                            <hr/>
                            <button type="submit" class="btn btn-success" title="Salvar"><i class="fa fa-save"></i> Salvar</button>
                            <a href="{{ url('perfis') }}" class="btn btn-danger" title="Cancelar"><i class="fa fa-times"></i> Cancelar</a>
                        </div>
                                    
                    {!! Form::close() !!}
                </div>
            </div>                     
        </div>
    </div>
</div> 
@endsection