@extends('layouts.app')
@section('style')
<style>
    .top-40 {
        margin-top: 40px!important;
    }
</style>
@endsection
@section('content')
<div class="col-md-12">
    {!! Form::open(['id' => 'frm_jornal_impresso_editar', 'url' => ['fonte-impresso'], 'method' => 'post']) !!}
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="card-title ml-3">
                            <i class="fa fa-newspaper-o"></i> Impressos
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i> Fontes Impressos
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i> Cadastrar
                        </h4>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('fonte-impresso/listar') }}" class="btn btn-primary pull-right mr-3"><i class="fa fa-table"></i> Fontes Impressos</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="col-md-12">
                    @include('layouts.mensagens')
                </div>
                <div class="row mr-1 ml-1">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Código</label>
                            <input type="text" class="form-control" name="codigo" id="codigo" placeholder="Código" value="{{ old('codigo') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tipo <span class="text-danger">Obrigatório</span></label>
                            <select class="form-control" name="tipo" id="tipo">
                                <option value="">Selecione um tipo</option>
                                <option value="1" {{ (old('tipo') == 1) ? 'selected' : '' }}>Jornal</option>
                                <option value="2" {{ (old('tipo') == 2) ? 'selected' : '' }}>Revista</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Coleta <span class="text-danger">Obrigatório</span></label>
                            <select class="form-control" name="coleta" id="coleta">
                                <option value="">Selecione a coleta</option>
                                <option value="1" {{ (old('coleta') == 1) ? 'selected' : '' }}>Coleta Web</option>
                                <option value="2" {{ (old('coleta') == 2) ? 'selected' : '' }}>Upload</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Modelo <span class="text-info">Somente para <strong>Coleta Web</strong></span></label>
                            <select class="form-control" name="modelo" id="modelo" disabled="disabled">
                                <option value="">Selecione a modelo</option>
                                @foreach ($modelos as $modelo)
                                    <option value="{{ $modelo->modelo }}" {!! old('modelo') == $modelo->modelo ? 'selected' : '' !!}>{{ $modelo->descricao }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Nome <span class="text-danger">Obrigatório</span></label>
                            <input type="text" class="form-control" name="nome" id="nome" placeholder="Nome" required value="{{ old('nome') }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>URL de Coleta <span class="text-danger">Obrigatório para fontes de <strong>Coleta Web</strong></span></label>
                            <input type="text" class="form-control" name="url" id="url" disabled="disabled" placeholder="URL de Coleta" {{ (old('coleta') == 2) ? 'disabled' : '' }} value="{{ old('url') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>País</label>
                            <select class="form-control select2" name="pais" id="pais">
                                <option value="">Selecione</option>
                                @foreach ($paises as $pais)
                                    <option value="{{ $pais->cd_pais }}" {!! old('cd_pais') == $pais->cd_pais ? " selected" : '' !!}>{{ $pais->ds_pais }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Estado</label>
                            <select class="form-control select2" name="cd_estado" id="cd_estado">
                                <option value="">Selecione</option>
                                @foreach ($estados as $estado)
                                    <option value="{{ $estado->cd_estado }}" {!! old('cd_estado') == $estado->cd_estado ? " selected" : '' !!}>{{ $estado->nm_estado }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Cidade </label>
                            <input type="hidden" name="cd_cidade" id="cd_cidade" value="{{ old('cd_cidade') }}">
                            <select class="form-control select2" name="cidade" id="cidade" disabled="disabled">
                                <option value="">Selecione uma cidade</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Valor Capa Semana</label>
                            <input type="text" class="form-control retorno_midia" name="valor_cm_capa_semana" id="valor_cm_capa_semana" placeholder="0,00" value="{{ number_format(old('valor_cm_capa_semana'), 2, ".","") }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Valor Capa FDS</label>
                            <input type="text" class="form-control retorno_midia" name="valor_cm_capa_fim_semana" id="valor_cm_capa_fim_semana" placeholder="0,00" value="{{ number_format(old('valor_cm_capa_fim_semana'), 2, ".","") }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Valor Contracapa</label>
                            <input type="text" class="form-control retorno_midia" name="valor_cm_contracapa" id="valor_cm_contracapa" placeholder="0,00" value="{{ number_format(old('valor_cm_contracapa'), 2, ".","") }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Valor Demais Semana</label>
                            <input type="text" class="form-control retorno_midia" name="valor_cm_demais_semana" id="valor_cm_demais_semana" placeholder="0,00" value="{{ number_format(old('valor_cm_demais_semana'), 2, ".","") }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Valor Demais FDS</label>
                            <input type="text" class="form-control retorno_midia" name="valor_cm_demais_fim_semana" id="valor_cm_demais_fim_semana" placeholder="0,00" value="{{ number_format(old('valor_cm_demais_fim_semana'), 2, ".","") }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check" style="margin-top: 30px;">
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" {{ (old('with_login')) ? 'checked' : '' }} type="checkbox" name="with_login" value="true">
                                        EXIGE LOGIN
                                    <span class="form-check-sign"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center mb-2">
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Salvar</button>
                <a href="{{ url('fonte-impresso/listar') }}" class="btn btn-danger ml-2"><i class="fa fa-times"></i> Cancelar</a>
            </div>
        </div>
    {!! Form::close() !!}
</div>
@endsection
@section('script')
    <script src="{{ asset('js/cropper-main.js') }}"></script>
    <script>
        $(document).ready(function(){

            let host =  $('meta[name="base-url"]').attr('content');

            $("#cd_estado").trigger("change");

            $("#coleta").change(function(){

                var coleta = $(this).val();

                if(coleta == 1){
                    $("#url").attr("disabled",false);
                    $("#modelo").attr("disabled",false);
                }else{
                    $("#url").attr("disabled",true);
                    $("#modelo").attr("disabled",true);
                    $("#url").val("");
                }
            });

        });
    </script>
@endsection