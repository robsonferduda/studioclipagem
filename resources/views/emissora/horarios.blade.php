@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-volume-up ml-3"></i> Rádio 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Emissoras 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Horários 
                    </h4>
                </div>
                <div class="col-md-4">
                    
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="col-md-12 px-0">
                {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['emissoras/horario/adicionar']]) !!}
                    <input type="hidden" name="id_emissora" value="{{ $id_emissora }}" />
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Horário Inicial</label>
                                        <input type="text" class="form-control horario" name="hora_inicial" id="hora_inicial" placeholder="00:00" value="">
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Horário Final</label>
                                        <input type="text" class="form-control horario" name="hora_final" id="hora_final" placeholder="00:00" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 checkbox-radios mb-0">
                                    <button type="submit" id="" class="btn btn-success mb-3"><i class="fa fa-plus"></i> Adicionar</button>
                                </div>
                            </div>     
                        </div>
                    {!! Form::close() !!} 
            </div>
            <div class="col-md-12">
                  
            </div>
        </div>
    </div>
</div> 
@endsection