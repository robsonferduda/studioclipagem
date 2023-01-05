@extends('layouts.app')
@section('content')
<div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="card-title ml-2"><i class="fa fa-signal"></i> Emissoras > Novo</h4>
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
                    {!! Form::open(['id' => 'frm_user_create', 'url' => ['emissora']]) !!}
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Estado <span class="text-danger">Obrigatório</span></label>
                                <select class="form-control" name="estado" id="estado" required="required">
                                    <option value="">Selecione um estado</option>
                                    @foreach($estados as $estado)
                                        <option value="{{ $estado->cd_estado }}">{{ $estado->nm_estado }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label>Cidade <span class="text-danger">Obrigatório</span></label>
                                <select class="form-control" name="cidade" id="cidade" required="required">
                                    <option value="">Selecione uma cidade</option>
                                    
                                </select>
                            </div>
                        </div>
                    </div>  
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Código <span class="text-danger">Obrigatório</span></label>
                                <input type="text" class="form-control" name="codigo" id="codigo" placeholder="Código" value="{{ old('codigo') }}" required="required">
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label>Nome <span class="text-danger">Obrigatório</span></label>
                                <input type="text" class="form-control" name="ds_emissora" id="ds_emissora" placeholder="Nome" value="{{ old('ds_emissora') }}" required="required">
                            </div>
                        </div>
                    </div>  
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mt-3">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" {{ ( old('is_active')) ? 'checked' : '' }} type="checkbox" name="is_active" value="true">
                                        Fazer Transcrição
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
@section('script')
    <script>
        $(document).ready(function() {

            var host =  $('meta[name="base-url"]').attr('content');
            
            $("#estado").change(function(){

                id = $(this).val();

                $.ajax({
                    url: host+'/estado/'+id+'/cidades',
                    type: 'GET',        
                    success: function(response) {
                        
                    },
                    error: function(xhr, status, error){
                        
                    }
                });    

            });
        });
    </script>
@endsection