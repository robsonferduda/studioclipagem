@extends('layouts.app')
@section('content')
<div class="col-md-12">

    {!! Form::open(['id' => 'frm_cliente_edit', 'url' => ['fonte-web', $fonte->id], 'method' => 'patch']) !!}
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-9">
                        <h4 class="card-title">
                            <i class="fa fa-newspaper-o"></i> Jornal Impresso
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
                    <div class="col-md-6">
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
                            <select class="form-control select2" name="cd_cidade" id="cd_cidade">
                                <option value="">Selecione uma cidade</option>
                                @foreach ($cidades as $cidade)
                                    <option value="{{ $cidade->cd_cidade }}" {{ ($fonte->cd_cidade == $cidade->cd_cidade) ? 'selected' : '' }}>{{ $cidade->nm_cidade }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>                   
                </div>
                <div class="row">
                    <div class="col-md-6">
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
                </div>  
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check mt-3">
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="checkbox" name="resetar_situacao" value="true" {{ ($fonte->id_situacao == 173 or $fonte->id_situacao == 174) ? 'checked' : '' }}>
                                    Resetar Situação 
                                    <span class="form-check-sign"></span>
                                </label>
                            </div>
                            <span>A opção <strong>Resetar Situação</strong> coloca a situação da fonte para <strong>Aguardando</strong>, retornando para a fila de processamento.</span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <span class="text-info"><strong>Atenção! </strong>Os campos para edição de notícia são disponibilizados apenas para as fontes com inconsistência de estrutura e mapeamento.</span>
                    </div>
                </div> 
                @if($fonte->id_situacao == 173 or $fonte->id_situacao == 174)
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
                @endif
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
