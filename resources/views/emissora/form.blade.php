@extends('layouts.app')
@section('content')
<div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="card-title ml-2"><i class="fa fa-signal"></i> Emissoras > {{ empty($emissora->id) ? 'Novo' :'Editar' }}</h4>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ url('emissoras/radio') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-signal"></i> Emissoras</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="col-md-12">
                    @include('layouts.mensagens')
                </div>
                <div class="col-md-12">
                    @if(empty($emissora->id))
                        {!! Form::open(['id' => 'frm_user_create', 'url' => ['emissora/'.$tipo.'/adicionar']]) !!}
                    @else
                        {!! Form::open(['id' => 'frm_noticia_radio_editar', 'url' => ['emissora/'. $emissora->id. '/atualizar'], 'method' => 'post']) !!}
                    @endif
                    <input type="hidden" name="tipo" value="{{ $tipo }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Estado <span class="text-danger">Obrigatório</span></label>
                                <select class="form-control selector-select2" name="cd_estado" id="cd_estado">
                                    <option value="">Selecione um estado</option>
                                    @foreach($estados as $estado)
                                        <option value="{{ $estado->cd_estado }}" {{ ($emissora) ? (($emissora->cd_estado == $estado->cd_estado) ? 'selected' : '' ) : '' }}>{{ $estado->nm_estado }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <input type="hidden" name="cd_cidade" id="cd_cidade" value="{{ ($emissora->cd_cidade) ? $emissora->cd_cidade : 0  }}">
                                <label>Cidade <span class="text-danger">Obrigatório</span></label>
                                <select class="form-control select2" name="cidade" id="cidade" disabled="disabled">
                                    <option value="">Selecione uma cidade</option>                                    
                                </select>
                            </div>
                        </div>
                    </div>  
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Código <span class="text-danger">Obrigatório</span></label>
                                <input type="text" class="form-control" name="codigo" id="codigo" placeholder="Código" value="{{ ($emissora) ? $emissora->codigo : old('codigo') }}" required="required">
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label>Nome <span class="text-danger">Obrigatório</span></label>
                                <input type="text" class="form-control" name="ds_emissora" id="ds_emissora" placeholder="Nome" value="{{ ($emissora) ? $emissora->ds_emissora : old('ds_emissora') }}" required="required">
                            </div>
                        </div>
                    </div>  
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mt-3">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" {{ (($emissora and $emissora->fl_transcricao) ? 'selected' : '') ? ((old('is_active')) ? 'checked' : '') : '' }} type="checkbox" name="is_active" value="true">
                                        Fazer Transcrição
                                        <span class="form-check-sign"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>          
                </div>
                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Salvar</button>
                    <a href="{{ url()->previous() }}" class="btn btn-danger"><i class="fa fa-times"></i> Cancelar</a>
                </div>
            </div>
        {!! Form::close() !!} 
</div> 
@endsection
@section('script')
    <script>
        $(document).ready(function() {

            var host =  $('meta[name="base-url"]').attr('content');

            $("#cd_estado").trigger('change');
            
        });
    </script>
@endsection