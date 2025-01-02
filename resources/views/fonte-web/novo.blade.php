@extends('layouts.app')
@section('content')
<div class="col-md-12">

    {!! Form::open(['id' => 'frm_cliente_edit', 'url' => ['fonte-web'], 'method' => 'post']) !!}
        <div class="card">
            <div class="card-header">
                <div class="row ml-1 mr-1">
                    <div class="col-md-6">
                        <h4 class="card-title">
                            <i class="fa fa-globe"></i> Web
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i> Fontes
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i> Novo
                        </h4>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ url('fonte-web/listar') }}" class="btn btn-warning pull-right"><i class="nc-icon nc-minimal-left"></i> Voltar</a>
                    </div>
                </div>
            </div>
            <div class="card-body ml-3 mr-3">
                <div class="row">
                    <div class="col-md-12">
                        @include('layouts.mensagens')
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>País <span class="text-danger">Obrigatório</span></label>
                            <select class="form-control select2" name="cd_pais" id="cd_pais">
                                <option value="">Selecione um país</option>
                                @foreach ($paises as $pais)
                                    <option value="{{ $pais->cd_pais }}" {{ ( old('cd_pais') == $pais->cd_pais) ? 'selected' : '' }}>{{ $pais->ds_pais }}</option>
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
                                    <option value="{{ $estado->cd_estado }}" {{ ( old('cd_estado') == $estado->cd_estado) ? 'selected' : '' }}>{{ $estado->nm_estado }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Cidade</label>
                            <select class="form-control select2" name="cd_cidade" id="cd_cidade">
                                <option value="">Selecione uma cidade</option>
                                @foreach ($cidades as $cidade)
                                    <option value="{{ $cidade->cd_cidade }}">{{ $cidade->nm_cidade }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>                   
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nome <span class="text-danger">Obrigatório</span></label>
                            <input type="text" class="form-control" name="nome" id="nome" placeholder="Nome" value="{{ old("nome") }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>URL <span class="text-danger">Obrigatório</span></label>
                            <input type="text" class="form-control" name="url" id="url" placeholder="URL" value="{{ old("url") }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Valor</label>
                            <input type="text" class="form-control" name="nu_valor" id="nu_valor" placeholder="0,00" value="{{ number_format(old('nu_valor'), 2, ".","") }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Prioridade</label>
                            <select class="form-control select2" name="id_prioridade" id="id_prioridade">
                                <option value="">Selecione uma prioridade</option>
                                @foreach ($prioridades as $prioridade)
                                    <option value="{{ $prioridade->id }}" {{ ($prioridade->id == old('id_prioridade')) ? 'selected' : '' }}>{{ $prioridade->ds_prioridade }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h6>Dados de Mapeamento</h6>
                    </div> 
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Título <span class="text-danger">Obrigatório</span></label>
                            <input type="text" class="form-control" name="titulo" id="titulo" placeholder="Título" value="">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Data Notícia <span class="text-danger">Obrigatório</span></label>
                            <input type="text" class="form-control datepicker" name="dt_clipagem" required="true" value="{{ date("d/m/Y") }}" placeholder="__/__/____">
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="form-group">
                            <label>Link da Notícia <span class="text-danger">Obrigatório</span></label>
                            <input type="text" class="form-control" name="link" id="link" placeholder="Link da Notícia" value="">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label for="sinopse">Texto <span class="text-danger">Obrigatório</span></label>
                        <div class="form-group">
                            <textarea class="form-control" name="texto" id="texto" rows="10"></textarea>
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

    </script>
@endsection
