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
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>País</label>
                                <select class="form-control select2" name="cd_pais" id="cd_pais">
                                    <option value="">Selecione um país</option>
                                    @foreach ($paises as $pais)
                                        <option value="{{ $pais->id }}" {{ ($emissora->cd_pais == $pais->id) ? 'selected' : '' }}>{{ $pais->ds_pais }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Estado</label>
                                <select class="form-control selector-select2" name="cd_estado" id="cd_estado">
                                    <option value="">Selecione um estado</option>
                                    @foreach($estados as $estado)
                                        <option value="{{ $estado->cd_estado }}" {{ ($emissora) ? (($emissora->cd_estado == $estado->cd_estado) ? 'selected' : '' ) : '' }}>{{ $estado->nm_estado }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="form-group">
                                <label>Cidade</label>
                                <select class="form-control select2" name="cd_cidade" id="cidade" disabled="disabled">
                                    <option value="">Selecione uma cidade</option>                                    
                                </select>
                            </div>
                        </div>
                    </div>  
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nome <span class="text-danger">Obrigatório</span></label>
                                <input type="text" class="form-control" name="nome_emissora" id="nome_emissora" placeholder="Nome" value="{{ ($emissora) ? $emissora->nome_emissora : old('nome_emissora') }}" required="required">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>URL</label>
                                <input type="text" class="form-control" name="url_stream" id="url_stream" placeholder="URL" value="{{ ($emissora) ? $emissora->url_stream : old('url_stream') }}">
                            </div>
                        </div>
                    </div>  
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Valor</label>
                                <input type="text" class="form-control" name="nu_valor" id="nu_valor" placeholder="Valor" value="{{ ($emissora) ? $emissora->nu_valor : old('nu_valor') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <div class="form-check" style="margin-top: 30px;">
                                    <label class="form-check-label">
                                        <input class="form-check-input" {{ (($emissora and $emissora->gravar or old('gravar')) ? 'checked' : '') }} type="checkbox" name="gravar" value="true">
                                        Fazer Gravação
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