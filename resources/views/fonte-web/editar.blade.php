@extends('layouts.app')
@section('content')
<div class="col-md-12">
    {!! Form::open(['id' => 'frm_cliente_edit', 'url' => ['fonte-web', $fonte->id], 'method' => 'patch']) !!}
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-9">
                        <h4 class="card-title">
                            <i class="fa fa-globe"></i> Web
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i> Fontes
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i> Editar
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i> {{ $fonte->nome }}
                        </h4>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ url('fonte-web/listar') }}" class="btn btn-warning pull-right"><i class="nc-icon nc-minimal-left"></i> Voltar</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        @include('layouts.mensagens')
                    </div>
                </div>
                <div class="row">
                    <input type="hidden" name="flag_inconsistencia" id="flag_inconsistencia" value="{{ $flag_inconsistencia }}">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>País <span class="text-danger">Obrigatório</span></label>
                            <select class="form-control select2" name="cd_pais" id="cd_pais">
                                <option value="">Selecione um país</option>
                                @foreach ($paises as $pais)
                                    <option value="{{ $pais->cd_pais }}" {{ ($fonte->cd_pais == $pais->cd_pais) ? 'selected' : '' }}>{{ $pais->ds_pais }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Estado <span class="text-danger">Obrigatório</span></label>
                            <select class="form-control select2" name="cd_estado" id="cd_estado">
                                <option value="">Selecione um estado</option>
                                @foreach ($estados as $estado)
                                    <option value="{{ $estado->cd_estado }}" {{ ($fonte->cd_estado == $estado->cd_estado) ? 'selected' : '' }}>{{ $estado->nm_estado }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Cidade <span class="text-danger">Obrigatório</span></label>
                            <select class="form-control select2" name="cd_cidade" id="cidade" disabled="disabled">
                                <option value="">Selecione uma cidade</option>
                                @foreach ($cidades as $cidade)
                                    <option value="{{ $cidade->cd_cidade }}" {{ ($fonte->cd_cidade == $cidade->cd_cidade) ? 'selected' : '' }}>{{ $cidade->nm_cidade }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>                   
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nome <span class="text-danger">Obrigatório</span></label>
                            <input type="text" class="form-control" name="nome" id="nome" placeholder="Nome" value="{{ $fonte->nome }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>URL</label>
                            <input type="text" class="form-control" name="url" id="url" placeholder="URL" value="{{ $fonte->url }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Valor</label>
                            <input type="text" class="form-control monetario" name="nu_valor" id="nu_valor" value="{{ number_format($fonte->nu_valor, 2, ".","") }}">
                        </div>
                    </div>
                </div>  
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Situação</label>
                            <select class="form-control select2" name="id_situacao" id="id_situacao">
                                <option value="">Selecione uma situação</option>
                                @foreach ($situacoes as $situacao)
                                    <option value="{{ $situacao->id_situacao_fonte_web }}" {{ ($situacao->id_situacao_fonte_web == $fonte->id_situacao) ? 'selected' : '' }}>{{ $situacao->ds_situacao }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Prioridade</label>
                            <select class="form-control select2" name="id_prioridade" id="id_prioridade">
                                <option value="">Selecione uma prioridade</option>
                                @foreach ($prioridades as $prioridade)
                                    <option value="{{ $prioridade->id }}" {{ ($prioridade->id == $fonte->id_prioridade) ? 'selected' : '' }}>{{ $prioridade->ds_prioridade }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check mt-3">
                            <input type="hidden" name="id_situacao_atual" id="id_situacao_atual" value="{{ $fonte->id_situacao }}">
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="checkbox" name="resetar_situacao" id="resetar_situacao" value="true">
                                    Resetar Situação 
                                    <span class="form-check-sign"></span>
                                </label>
                            </div>
                            <span>A opção <strong>Resetar Situação</strong> coloca a situação da fonte para <strong>Aguardando</strong>, retornando para a fila de processamento.</span>
                        </div>
                    </div>
                </div> 
            </div>
            <div class="card-footer text-center mb-3">
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Salvar</button>
                <a href="{{ url('fonte-web/listar') }}" class="btn btn-danger"><i class="fa fa-times"></i> Cancelar</a>
            </div>
        </div>
    {!! Form::close() !!}
</div>
@endsection
@section('script')
    <script>
        $(document).ready(function(){
            
            $("#cd_estado").trigger("change");
            $("#resetar_situacao").trigger("click");
            $("#id_situacao").val(0).change();

            $(document).on('click', '#resetar_situacao', function() {

                var id_situacao_atual = $("#id_situacao_atual").val();

                if($("#resetar_situacao").is(':checked')) {
                    $("#id_situacao").val(0).change();
                }else{
                    $("#id_situacao").val(id_situacao_atual).change();
                }

            });

            $(document).on('change', '#id_situacao', function() {

                var situacao = $(this).val();

                if(situacao != 0) 
                    $("#resetar_situacao").prop('checked', false);
                else
                    $("#resetar_situacao").prop('checked', true);
            });

        });
    </script>
@endsection
