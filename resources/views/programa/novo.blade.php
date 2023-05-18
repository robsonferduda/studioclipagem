@extends('layouts.app')
@section('content')
<div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6 mt-3">
                        <h4 class="card-title ml-2">
                            <i class="fa fa-volume-up ml-3"></i> Rádio 
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i> Emissoras
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i> Programa
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i> Cadastrar
                        </h4>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ url('radio/emissoras') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-signal"></i> Emissoras</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="col-md-12">
                    @include('layouts.mensagens')
                </div>
                <div class="col-md-12">
                    {!! Form::open(['id' => 'frm_user_create', 'url' => ['programa']]) !!}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Emissora <span class="text-danger">Obrigatório</span></label>
                                <select class="form-control select2" name="emissora_id" id="emissora_id" required="required">
                                    <option value="">Selecione uma emissora</option>
                                    @foreach($emissoras as $emissora)
                                        <option value="{{ $emissora->id }}">{{ $emissora->ds_emissora }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Nome <span class="text-danger">Obrigatório</span></label>
                                <input type="text" class="form-control" name="nome" id="nome" placeholder="Nome" value="{{ old('nome') }}" required="required">
                            </div>
                        </div>
                    </div>  
                </div>
                <div class="card-footer text-center mb-3">
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Salvar</button>
                    <a href="{{ url('usuarios') }}" class="btn btn-danger"><i class="fa fa-times"></i> Cancelar</a>
                </div>
            </div>
        {!! Form::close() !!} 
</div> 
@endsection
@section('script')
    <script>
        $(document).ready(function() {

            var host =  $('meta[name="base-url"]').attr('content');
            
            $("#estado").change(function(){

                id = $(this).val();

                $.ajax({
                    url: host+'/estado/'+id+'/cidades',
                    type: 'GET',        
                    success: function(data) {

                        $('#cidade').empty();
                        $('#cidade').append($('<option>', { 
                                value: "",
                                text : "Selecione uma cidade" 
                        }));

                        $.each(data, function(index, value) {

                            $('#cidade').append($('<option>', { 
                                value: value.cd_cidade,
                                text : value.nm_cidade 
                            }));
                        });                        
                    },
                    error: function(xhr, status, error){
                        
                    }
                });    

            });
        });
    </script>
@endsection