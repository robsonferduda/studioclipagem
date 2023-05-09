@extends('layouts.app')
@section('style')
<style>
    .top-40 {
        margin-top: 40px!important;
    }
    .hide{
        display: none;
    }
    #filename {
        height: 41px;
        top: 10px;
    }
</style>
@endsection
@section('content')
<div class="col-md-12">
    @if(empty($dados->id))
        {!! Form::open(['id' => 'frm_noticia_radio_criar', 'url' => ['radio/noticias/inserir'], 'method' => 'post', 'files' => true]) !!}
    @else
        {!! Form::open(['id' => 'frm_noticia_radio_editar', 'url' => ['radio/noticias/'. $dados->id. '/atualizar'], 'method' => 'post', 'files' => true]) !!}
    @endif
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="card-title ml-2"><i class="fa fa-newspaper-o"></i> Rádio
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Notícias
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> {!! empty($dados->id) ? 'Cadastrar' : 'Atualizar' !!}</h4>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ url('radio/noticias') }}" class="btn btn-primary pull-right mr-3"><i class="fa fa-table"></i> Notícias</a>
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
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Título <span class="text-danger">Obrigatório</span></label>
                            <input type="text" class="form-control" name="titulo" id="titulo" placeholder="Título" required value="{{ $dados->titulo }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Data <span class="text-danger">Obrigatório</span></label>
                            <input type="date" class="form-control" name="data" id="data" required value="{!! !empty($dados->dt_noticia) ? date('Y-m-d', strtotime($dados->dt_noticia)) : '' !!}">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Cliente <span class="text-danger">Obrigatório</span></label>
                            <select class="form-control selector-select2" name="cliente" id="cliente" required>
                                @if(!empty($dados->cliente_id))
                                    <option value="{!! $dados->cliente->id !!}">{!! $dados->cliente->pessoa->nome !!}</option>
                                @else
                                    <option value="">Selecione</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Área</label>
                            <select class="form-control selector-select2" name="area" id="area" {!! !empty($dados->area_id) ? '' : 'disabled' !!}>
                                <option value="">Selecione</option>
                                @foreach($areas as $area)
                                    <option value="{!! $area->id !!}" {!! $dados->area_id == $area->id ? 'selected' : '' !!}>
                                        {!! $area->descricao !!}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Estado </label>
                            <select class="form-control selector-select2" name="estado" id="estado">
                                <option value="">Selecione</option>
                                @foreach ($estados as $estado)
                                    <option value="{{ $estado->cd_estado }}" {!! $dados->cd_estado == $estado->cd_estado ? " selected" : '' !!}>
                                        {{ $estado->nm_estado }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Cidade </label>
                            <select class="form-control selector-select2" name="cidade" id="cidade" {!! $dados->cd_estado ? '' : 'disabled="disabled"' !!}>
                                <option value="">{!! $dados->cd_estado ? 'Selecione' : 'Selecione o estado' !!}</option>
                                @foreach ($cidades as $cidade)
                                    <option value="{{ $cidade->cd_cidade }}" {!! $dados->cd_cidade == $cidade->cd_cidade ? 'selected' : '' !!}>{{ $cidade->nm_cidade }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Emissora <span class="text-danger">Obrigatório</span></label>
                            <select class="form-control" name="emissora" id="emissora" required>
                            <option value="">Selecione</option>
                                @if(!empty($dados->emissora->id))
                                    <option value="{!! $dados->emissora_id !!}" selected>{!! $dados->emissora->ds_emissora !!}</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Programa</label>
                            <select class="form-control selector-select2" name="programa" id="programa" {!! !empty($dados->programa_id ? '' : 'disabled') !!}>
                                <option value="">Selecione</option>
                                @if(!empty($dados->programa->id))
                                    <option value="{!! $dados->programa_id !!}" selected>{!! $dados->programa->nome !!}</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <!-- Falta o campo no banco -->
                    <!-- <div class="col-md-8">
                        <div class="form-group">
                            <label>Link</label>
                            <input type="text" class="form-control" name="link" id="link" placeholder="Link" required value="{{ $dados->link }}">
                        </div>
                    </div> -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Foi para boletim automático</label>
                            <select class="form-control" name="boletim" id="boletim">
                                <option value="S" {!! $dados->fl_boletim  ? 'selected' : '' !!}>Sim</option>
                                <option value="N" {!! !$dados->fl_boletim ? 'selected' : '' !!}>Não</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <label for="arquivo">Arquivo</label>
                        <div class="upload-arquivo {!! empty($dados->arquivo) ? '' : 'hide' !!}">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <button
                                        class="btn btn-outline-info selecionar-arquivo"
                                        id="btn-arquivo" type="button"
                                    ><i class="fa fa-upload" aria-hidden="true"></i> Escolher Arquivo</button>
                                </div>
                                <input
                                    type="text" class="form-control"
                                    id="filename" placeholder="Nenhum arquivo escolhido" disabled
                                />
                                <input type="file" name="arquivo" id="arquivo" style="display: none;">
                            </div>
                        </div>
                        <div class="input-group mb-3 download-arquivo {!! empty($dados->arquivo) ? 'hide' : '' !!}">
                            <div class="mr-2" style="margin-top: -10px">
                                <a
                                    class="btn btn-outline-primary baixar-arquivo"
                                    href="/radio/noticias/{{$dados->id}}/download"
                                    target="_blank"
                                ><i class="fa fa-download" aria-hidden="true"></i>  Baixar Arquivo</a>
                            </div>
                            <div class="mr-2" style="margin-top: -10px">
                                <button
                                    class="btn btn-outline-danger remover-arquivo"
                                    id="remover-arquivo" type="button"
                                    data-file="{!! $dados->id !!}"
                                ><i class="fa fa-trash" aria-hidden="true"></i> Remover Arquivo</button>
                            </div>
                            <input type="hidden" name="remover" id="remover" value="false">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center mb-2">
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Salvar</button>
                <a href="{{ url('radio/noticias') }}" class="btn btn-danger ml-2"><i class="fa fa-times"></i> Cancelar</a>
            </div>
        </div>
    {!! Form::close() !!}
</div>
@endsection
@section('script')
    <script>
        $(document).ready(function(){
            $('.selector-select2').select2({
                placeholder: 'Selecione',
                allowClear: true
            });

            $('#cliente').select2({
                placeholder: 'Selecione',
                allowClear: true,
                ajax: {
                    url: "/api/cliente/buscarClientes",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;

                        return {
                            results: data.data,
                            pagination: {
                                more: (params.page * 30) < data.total
                            }
                        };
                    },
                },
            });

            $('#emissora').select2({
                placeholder: 'Selecione',
                allowClear: true,
                ajax: {
                    url: "/api/emissora/buscarEmissoras",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page || 1,
                            estado: $('#estado').val(),
                            cidade: $('#cidade').val()
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;

                        return {
                            results: data.data,
                            pagination: {
                                more: (params.page * 30) < data.total
                            }
                        };
                    },
                },
                templateResult: function(result) {
                    if (result.loading) {
                        return 'Buscando...';
                    }
                    return `${result.text} - ${result.cidade}`
                }
            });

            $('#programa').select2({
                placeholder: 'Selecione',
                allowClear: true,
                ajax: {
                    url: "/api/programa/buscarProgramas",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page || 1,
                            emissora: $('#emissora').val()
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;

                        return {
                            results: data.data,
                            pagination: {
                                more: (params.page * 30) < data.total
                            }
                        };
                    },
                },
            });
        });

        $(document).on("click", ".selecionar-arquivo", function() {
            $('#arquivo').trigger('click');
        });

        $(document).on('change', '#arquivo', function() {
            let filename = ''
            if($(this).val() != '') {
                filename = $('#arquivo').val().replace(/C:\\fakepath\\/i, '');
            }
            $('#filename').val(filename);
        });

        $(document).on('click', '#remover-arquivo', function() {
            $('#remover').val(true);
            $('.upload-arquivo').slideDown();
            $('.download-arquivo').slideUp();
        })

        $(document).on('change', '#emissora', function() {
            $('#programa').find('option').remove().end();
            if($(this).val() == '') {
                return $('#programa').prop('disabled', true);
            }

            return $('#programa').prop('disabled', false);
        });

        $(document).on('change', '#estado', function() {
            $('#emissora').find('option').remove().end();
            $('#programa').find('option').remove().end();
        });

        $(document).on('change', '#estado', function() {
            $('#cidade').find('option').remove().end();
            $('#emissora').find('option').remove().end();
            $('#programa').find('option').remove().end();

            if($(this).val() == '') {
                $('#cidade').attr('disabled', true);
                $('#cidade').append('<option value="">Selecione</option>').val('');
                return;
            }

            $('#cidade').append('<option value="">Carregando...</option>').val('');

            $.ajax({
                url: '/api/estado/getCidades',
                type: 'GET',
                data: {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                    "estado": $(this).val(),
                },
                beforeSend: function() {
                    $('.content').loader('show');
                },
                success: function(data) {
                    if(!data) {
                        Swal.fire({
                            text: 'Não foi possível buscar as cidades. Por favor, tente novamente mais tarde',
                            type: "warning",
                            icon: "warning",
                        });
                        return;
                    }
                    $('#cidade').attr('disabled', false);
                    $('#cidade').find('option').remove().end();

                    data.forEach(element => {
                        let option = new Option(element.nm_cidade, element.cd_cidade);
                        $('#cidade').append(option);
                    });
                    $('#cidade').val('');
                    $('#cidade').select2('destroy');
                    $('#cidade').select2({placeholder: 'Selecione', allowClear: true});
                },
                complete: function(){
                    $('.content').loader('hide');
                }
            });
        });

        $(document).on('change', '#cliente', function() {
            $('#area').find('option').remove().end();

            if($(this).val() == '') {
                $('#area').attr('disabled', true);
                $('#area').append('<option value="">Selecione</option>').val('');
                return;
            }

            $('#area').append('<option value="">Carregando...</option>').val('');

            $.ajax({
                url: '/api/cliente/getAreasCliente',
                type: 'GET',
                data: {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                    "cliente": $(this).val(),
                },
                beforeSend: function() {
                    $('.content').loader('show');
                },
                success: function(data) {
                    if(!data) {
                        Swal.fire({
                            text: 'Não foi possível buscar as áreas. Por favor, tente novamente mais tarde',
                            type: "warning",
                            icon: "warning",
                        });
                        return;
                    }
                    $('#area').attr('disabled', false);
                    $('#area').find('option').remove().end();

                    data.forEach(element => {
                        let option = new Option(element.descricao, element.id);
                        $('#area').append(option);
                    });
                    $('#area').val('');
                    $('#area').select2('destroy');
                    $('#area').select2({placeholder: 'Selecione', allowClear: true});
                },
                complete: function(){
                    $('.content').loader('hide');
                }
            });
        });
    </script>
@endsection
