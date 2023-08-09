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
                        <a href="{{ url('radios') }}" class="btn btn-warning pull-right mr-3"><i class="nc-icon nc-chart-pie-36"></i> Dashboard</a>
                        <a href="{{ url('radio/noticias') }}" class="btn btn-info pull-right mr-3"><i class="fa fa-table"></i> Notícias</a>
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
                    <div class="col-md-12 mt-3">
                        <h6>Clientes Vinculados</h6>
                    </div>
                    
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Cliente <span class="text-danger">Obrigatório</span></label>
                                <select class="form-control selector-select2" name="cliente" id="cliente">
                                    @if(!empty($dados->cliente_id))
                                        <option value="{!! $dados->cliente->id !!}">{!! $dados->cliente->pessoa->nome !!}</option>
                                    @else
                                        <option value="">Selecione</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Área <span class="text-info">Opcional</span></label>
                                <select class="form-control selector-select2" name="area" id="area" {!! !empty($dados->area_id) ? '' : 'disabled' !!}>
                                    <option value="">Selecione</option>
                                    @foreach($areas as $area)
                                        <option value="{!! $area->id !!}">
                                            {!! $area->descricao !!}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Sentimento <span class="text-info">Opcional</span></label>
                                <select class="form-control" name="sentimento" id="sentimento">
                                    <option value="">Selecione</option>
                                    <option value="1">Positivo</option>
                                    <option value="0">Neutro</option>
                                    <option value="-1">Negativo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group mt-3">
                                <button type="button" class="btn btn-success btn-add-cliente"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <ul class="list-unstyled metadados"></ul>
                        </div>
                    
                    <div class="col-md-12">
                        <h6>Dados da Notícia</h6>
                    </div>
                    <input type="hidden" name="clientes[]" id="clientes">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Data <span class="text-danger">Obrigatório</span></label>
                            <input type="text" class="form-control datepicker" name="data" id="data" placeholder="__/__/____" required value="{!! !empty($dados->dt_noticia) ? date('d/m/Y', strtotime($dados->dt_noticia)) : '' !!}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Duração <span class="text-danger">Obrigatório</span></label>
                            <input type="text" class="form-control duracao" name="duracao" id="duracao" placeholder="00:00:00" value="{{ $dados->duracao }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
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
                    <div class="col-md-4">
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
                    <div class="col-md-5">
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
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Horário</label>
                            <input type="text" class="form-control horario" name="horario" id="horario" placeholder="Horário">
                        </div>
                    </div>
                    <div class="col-md-5">
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
                    <div class="col-md-12">
                        <div class="form-group">    
                            <label for="tags[]">TAGs</label>
                            <select name="tags[]" multiple="multiple" class="form-control select2">
                                @foreach ($tags as $tag)
                                    <option value="{{ $tag->id }}">{{ $tag->nome }}</option>
                                @endforeach
                            </select> 
                        </div>    
                    </div> 
                    <div class="col-md-3">
                        <label for="arquivo">Arquivo</label>
                        <div style="min-height: 302px;" class="dropzone" id="dropzone"><div class="dz-message" data-dz-message><span>CLIQUE AQUI<br/> ou <br/>ARRASTE</span></div></div>
                        <input type="hidden" name="arquivo" id="arquivo">
                    </div>
                    <div class="col-md-9">
                        <label for="sinopse">Sinopse</label>
                        <div class="form-group">
                            <textarea class="form-control" name="sinopse" id="sinopse" rows="10">{!! nl2br($dados->sinopse) !!}</textarea>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Link</label>
                            <input type="text" class="form-control" name="link" id="link" placeholder="Link" value="{{ $dados->link }}">
                        </div>
                    </div>
                    
                    
                </div>
            </div>
            <div class="card-footer text-center mb-2">
                <button type="submit" class="btn btn-success" name="btn_enviar" value="salvar"><i class="fa fa-save"></i> Salvar</button>
                <button type="submit" class="btn btn-warning" name="btn_enviar" value="salvar_e_copiar"><i class="fa fa-copy"></i> Salvar e Copiar</button>
                <a href="{{ url('radio/noticias') }}" class="btn btn-danger"><i class="fa fa-times"></i> Cancelar</a>
            </div>
        </div>
    {!! Form::close() !!}
</div>
@endsection
@section('script')    
    <script>
        Dropzone.autoDiscover = false;
        $(document).ready(function(){

            var host =  $('meta[name="base-url"]').attr('content');
            var token =  $('meta[name="csrf-token"]').attr('content');
            
            $('.selector-select2').select2({
                placeholder: 'Selecione',
                allowClear: true
            });

            $(".dropzone").dropzone({ 
                acceptedFiles: ".mp3",
                maxFiles: 1,
                url: host+"/radio/noticias/upload",
                headers: {
                    'x-csrf-token': token,
                },
                success: function(file, responseText){
                    $("#arquivo").val(responseText.arquivo);
                    $("#duracao").val(responseText.duracao);

                    $.notify({
                        icon: 'fa fa-bell',
                        message: "<b>Mensagem do Sistema</b><br/> Arquivo enviado e duração do arquivo registrada com sucesso"
                    },{
                        type: 'info',
                        timer: 1000
                    });
                }
            });

            $('#cliente').select2({
                placeholder: 'Selecione',
                allowClear: true,
                ajax: {
                    url: host+"/api/cliente/buscarClientes",
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
                    url: host+"/api/emissora/buscarEmissoras",
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
                    url: host+"/api/programa/buscarProgramas",
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

        $(document).on("click", ".btn-add-cliente", function(clientes) {

            
            var id_cliente = $("#cliente").val();
            if(id_cliente){
                var cliente = $("#cliente option:selected").text();

                var id_area = $("#area").val();
                if(id_area)
                    var area = $("#area option:selected").text();
                else
                    var area = 'Nenhuma área selecionada';

                var id_sentimento = $("#sentimento").val();
                if(id_sentimento)
                    var sentimento = $("#sentimento option:selected").text();
                else
                    var sentimento = "Nenhum sentimento selecionado";

                
                var dados = { id_cliente: id_cliente, cliente: cliente, id_area: id_area, area: area, id_sentimento: id_sentimento, sentimento: sentimento };
                inicializaClientes(dados);
            }else{
                Swal.fire({
                    text: 'Obrigatório informar um cliente.',
                    type: "warning",
                    icon: "warning",
                    confirmButtonText: '<i class="fa fa-check"></i> Ok',
                });
            }
        });

        function inicializaClientes(dados){

            clientes.push(dados);
            $("#clientes").val(JSON.stringify(clientes));

            $(".metadados").empty();

            $.each(clientes, function(index, value) {                
                $(".metadados").append('<li><div class="row"><div class="col-md-12 col-12 mb-2"><span>'+value.cliente+'</span> | <span>'+value.area+'</span> | <span>'+value.sentimento+'</span> | <span class="text-danger btn-remover-cliente" data-id="'+index+'">Excluir</span></div></div></li>');
            });
        }

        var clientes = [];

        $(document).on('click', '.btn-remover-cliente', function() {
            
            id = $(this).data("id");
            
            clientes.splice(id, 1);
            $("#clientes").val(JSON.stringify(clientes));

            $(".metadados").empty();
            $.each(clientes, function(index, value) {                
                $(".metadados").append('<li><div class="row"><div class="col-md-9 col-9"><span>'+value.cliente+'</span> | <span>'+value.area+'</span> | <span>'+value.sentimento+'</span></div><div class="col-md-3 col-3 text-right"><btn class="btn btn-sm btn-outline-danger btn-round btn-icon btn-remover-cliente" data-id="'+index+'"><i class="fa fa-times"></i></btn></div></div></li>');
            });
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
            
            var host =  $('meta[name="base-url"]').attr('content');

            $.ajax({
                url: host+'/api/estado/getCidades',
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

            var host =  $('meta[name="base-url"]').attr('content');

            $.ajax({
                url: host+'/api/cliente/getAreasCliente',
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
