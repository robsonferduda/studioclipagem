@extends('layouts.app')
@section('content')
<div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="card-title ml-2">
                            <i class="fa fa-tv"></i> TV 
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i> Emissoras
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i> Programas
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i> {{ empty($programa->id) ? 'Novo' :'Editar' }}
                        </h4>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ url('tv/emissoras/programas') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-tv"></i> Programas</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="col-md-12">
                    @include('layouts.mensagens')
                </div>
                <div class="col-md-12">
                    @if(empty($programa->id))
                        {!! Form::open(['id' => 'frm_user_create', 'url' => ['tv/emissoras/programas/adicionar']]) !!}
                    @else
                        {!! Form::open(['id' => 'frm_noticia_radio_editar', 'url' => ['tv/emissoras/programas/atualizar'], 'method' => 'post']) !!}
                    @endif
                    <input type="hidden" name="id" value="{{ ($programa) ? $programa->id : '' }}">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>País</label>
                                <select class="form-control select2" name="cd_pais" id="cd_pais">
                                    <option value="">Selecione um país</option>
                                    @foreach ($paises as $pais)
                                        <option value="{{ $pais->cd_pais }}" {{ ($programa and $programa->cd_pais == $pais->cd_pais) ? 'selected' : '' }}>{{ $pais->ds_pais }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Estado</label>
                                <select class="form-control select2" name="cd_estado" id="cd_estado">
                                    <option value="">Selecione um estado</option>
                                    @foreach ($estados as $estado)
                                        <option value="{{ $estado->cd_estado }}" {{ ($programa and $programa->cd_estado == $estado->cd_estado) ? 'selected' : '' }}>{{ $estado->nm_estado }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Cidade</label>
                                <select class="form-control select2" name="cd_cidade" id="cidade" disabled="disabled">
                                    <option value="">Selecione uma cidade</option>
                                    @foreach ($cidades as $cidade)
                                        <option value="{{ $cidade->cd_cidade }}" {{ ($programa and $programa->cd_cidade == $cidade->cd_cidade) ? 'selected' : '' }}>{{ $cidade->nm_cidade }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>                   
                    </div>               
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Emissora <span class="text-danger">Obrigatório</span></label>
                                <select class="form-control" name="id_emissora" id="id_emissora" required>
                                    <option value="">Selecione uma emissora</option>
                                    @foreach ($emissoras as $emi)
                                        <option value="{{ $emi->id }}" {{ ($programa and $programa->id_emissora == $emi->id) ? 'selected' : '' }}>{{ $emi->nome_emissora }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tipo de Programa <span class="text-danger">Obrigatório</span></label>
                                <select class="form-control" name="tipo_programa" id="tipo_programa" required>
                                    <option value="">Selecione um tipo</option>
                                    @foreach ($tipos as $tipo)
                                        <option value="{{ $tipo->id }}" {{ ($programa and $programa->tipo_programa == $tipo->id) ? 'selected' : '' }}>{{ $tipo->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nome <span class="text-danger">Obrigatório</span></label>
                                <input type="text" class="form-control" name="nome_programa" id="nome_programa" placeholder="Nome" value="{{ ($programa) ? $programa->nome_programa : old('nome_programa') }}" required="required">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>URL</label>
                                <input type="text" class="form-control" name="url" id="url" placeholder="URL" value="{{ ($programa) ? $programa->url : old('url') }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>IP Local</label>
                                <input type="text" class="form-control" name="ip_local" id="ip_local" placeholder="IP Local" value="{{ ($programa) ? $programa->ip_local : old('ip_local') }}">
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