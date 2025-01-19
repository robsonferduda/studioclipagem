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
    {!! Form::open(['id' => 'frm_jornal_impresso_editar', 'url' => ['fonte-impresso/'. $fonte->id. '/atualizar'], 'method' => 'post']) !!}
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="card-title ml-3">
                            <i class="fa fa-newspaper-o"></i> Impressos
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i> Fontes
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i> Editar
                        </h4>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('fonte-impresso/listar') }}" class="btn btn-primary pull-right mr-3"><i class="fa fa-table"></i> Fontes Impressos</a>
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
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Código</label>
                            <input type="text" class="form-control" name="codigo" id="codigo" placeholder="Código" value="{{ $fonte->codigo }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tipo <span class="text-danger">Obrigatório</span></label>
                            <select class="form-control" name="tipo" id="tipo">
                                <option value="">Selecione um tipo</option>
                                <option value="1" {{ ($fonte->tipo == 1) ? 'selected' : '' }}>Jornal</option>
                                <option value="2" {{ ($fonte->tipo == 2) ? 'selected' : '' }}>Revista</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Coleta <span class="text-danger">Obrigatório</span></label>
                            <select class="form-control" name="coleta" id="coleta">
                                <option value="">Selecione a coleta</option>
                                <option value="1" {{ ($fonte->coleta == 1) ? 'selected' : '' }}>Coleta Web</option>
                                <option value="2" {{ ($fonte->coleta == 2) ? 'selected' : '' }}>Upload</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Modelo <span class="text-info">Somente para <strong>Coleta Web</strong></span></label>
                            <select class="form-control" name="modelo" id="modelo" {{ ($fonte->coleta == 2) ? 'disabled="disabled"' : '' }}>
                                <option value="">Selecione a modelo</option>
                                @foreach ($modelos as $modelo)
                                    <option value="{{ $modelo->modelo }}" {!! $fonte->modelo == $modelo->modelo ? " selected" : '' !!}>{{ $modelo->descricao }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Nome <span class="text-danger">Obrigatório</span></label>
                            <input type="text" class="form-control" name="nome" id="nome" placeholder="Nome" required value="{{ $fonte->nome }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>URL de Coleta <span class="text-info">Obrigatório para fontes de <strong>Coleta Web</strong></span></label>
                            <input type="text" class="form-control" name="url" id="url" placeholder="URL de Coleta" {{ ($fonte->coleta == 2) ? 'disabled' : '' }} value="{{ $fonte->url }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>País</label>
                            <input type="hidden" name="cd_pais" id="cd_pais" value="{{ $fonte->cd_pais }}">
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
                            <select class="form-control" name="cd_estado" id="cd_estado">
                                <option value="">Selecione</option>
                                @foreach ($estados as $estado)
                                    <option value="{{ $estado->cd_estado }}" {!! $fonte->cd_estado == $estado->cd_estado ? " selected" : '' !!}>{{ $estado->nm_estado }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Cidade </label>
                            <input type="hidden" name="cd_cidade" id="cd_cidade" value="{{ $fonte->cd_cidade }}">
                            <select class="form-control select2" name="cidade" id="cidade" disabled="disabled">
                                <option value="">Selecione uma cidade</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Valor Capa Semana</label>
                            <input type="text" class="form-control retorno_midia" name="valor_cm_capa_semana" id="valor_cm_capa_semana" placeholder="0,00" value="{{ number_format($fonte->valor_cm_capa_semana, 2, ".","") }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Valor Capa FDS</label>
                            <input type="text" class="form-control retorno_midia" name="valor_cm_capa_fim_semana" id="valor_cm_capa_fim_semana" placeholder="0,00" value="{{ number_format($fonte->valor_cm_capa_fim_semana, 2, ".","") }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Valor Contracapa</label>
                            <input type="text" class="form-control retorno_midia" name="valor_cm_contracapa" id="valor_cm_contracapa" placeholder="0,00" value="{{ number_format($fonte->valor_cm_contracapa, 2, ".","") }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Valor Demais Semana</label>
                            <input type="text" class="form-control retorno_midia" name="valor_cm_demais_semana" id="valor_cm_demais_semana" placeholder="0,00" value="{{ number_format($fonte->valor_cm_demais_semana, 2, ".","") }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Valor Demais FDS</label>
                            <input type="text" class="form-control retorno_midia" name="valor_cm_demais_fim_semana" id="valor_cm_demais_fim_semana" placeholder="0,00" value="{{ number_format($fonte->valor_cm_demais_fim_semana, 2, ".","") }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">                        
                            <div class="col-md-2">
                                <div class="form-check" style="margin-top: 15px;">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input class="form-check-input" {{ ($fonte->with_login) ? 'checked' : '' }} type="checkbox" name="with_login" value="true">
                                                EXIGE LOGIN
                                            <span class="form-check-sign"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-check" style="margin-top: 15px;">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input class="form-check-input" {{ ($fonte->fl_ativo) ? 'checked' : '' }} type="checkbox" name="fl_ativo" value="true">
                                                ATIVO
                                            <span class="form-check-sign"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check" style="margin-top: 15px;">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input class="form-check-input" {{ ($fonte->mapeamento_matinal) ? 'checked' : '' }} type="checkbox" name="mapeamento_matinal" value="true">
                                                PREFERÊNCIA DE PROCESSAMENTO
                                            <span class="form-check-sign"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <p class="mb-1 mt-3 text-info">
                            <i class="fa fa-envelope"></i> Seções 
                        </p>
                        <div class="row">
                            <div class="col-md-3 col-sm-12">
                                <div class="form-group">
                                    <label>Nome da Seção</label>
                                    <input type="text" class="form-control" name="ds_sessao" id="ds_sessao">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mt-3 ">
                                    <button type="button" class="btn btn-success btn-salvar-secao"><i class="fa fa-plus"></i> Adicionar Seção</button>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-md-12">
                                <h6>Seções Cadastradas</h6>
                                @if(count($fonte->secoes))
                                    @foreach($fonte->secoes as $secao)                                        
                                        <span data-id="{{ $secao->id_sessao_impresso }}">{{ $secao->ds_sessao }}<a title="Remover" class="btn-excluir-generico" href="{{ url('fonte-impresso/secao/excluir', $secao->id_sessao_impresso) }}"><i class="fa fa-trash text-danger ml-1 mr-3"></i></a></span>
                                    @endforeach
                                @else
                                    <p class="text-danger">Nenhuma seção cadastrada</p>
                                @endif
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

            var cd_pais = $("#cd_pais").val();
            $('#pais').val(cd_pais).change();

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

            $(".btn-salvar-secao").click(function(){

                var ds_sessao = $("#ds_sessao").val();

                $.ajax({
                        url: host+'/fonte-impresso/secao',
                        type: 'POST',
                        data: {
                                "_token": $('meta[name="csrf-token"]').attr('content'),
                                "ds_sessao": ds_sessao,
                                "font_id": {{ $fonte->id }}
                        },
                        success: function(response) {
                            location.reload();                    
                        },
                        error: function(response){
                            
                        }
                    });
            });
        });
    </script>
@endsection
