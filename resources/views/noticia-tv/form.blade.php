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
        {!! Form::open(['id' => 'frm_noticia_radio_criar', 'url' => ['tv/noticias/inserir'], 'method' => 'post', 'files' => true]) !!}
    @else
        {!! Form::open(['id' => 'frm_noticia_radio_editar', 'url' => ['tv/noticias/'. $dados->id. '/atualizar'], 'method' => 'post', 'files' => true]) !!}
    @endif
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="card-title ml-2"><i class="fa fa-tv"></i> TV
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Notícias
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> {!! empty($dados->id) ? 'Cadastrar' : 'Atualizar' !!}</h4>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ url('tv/dashboard') }}" class="btn btn-warning pull-right mr-3"><i class="nc-icon nc-chart-pie-36"></i> Dashboard</a>
                        <a href="{{ url('tv/noticias') }}" class="btn btn-info pull-right mr-3"><i class="fa fa-tv"></i> Notícias</a>
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
                    <div class="col-md-12">
                        <h6>Dados da Notícia</h6>
                    </div>
                    <input type="hidden" name="clientes[]" id="clientes">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Cliente <span class="text-info add-clientes" data-toggle="modal" data-target="#modalCliente">Adicionar Clientes</span></label>
                            <input hidden name="cliente_id" id="cliente_id" value="{{ ($cliente) ? $cliente->cliente_id : '' }}">
                            <select class="form-control cliente select2" name="cd_cliente" id="cd_cliente">
                                <option value="">Selecione um cliente</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Área do Cliente</label>
                            <input hidden name="area_id" id="area_id" value="{{ ($cliente) ? $cliente->area : '' }}">
                            <select class="form-control area select2" name="cd_area" id="cd_area" disabled>
                                <option value="">Selecione uma área</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Sentimento </label>
                            <select class="form-control" name="cd_sentimento" id="cd_sentimento">
                                <option value="">Selecione um sentimento</option>
                                <option value="1" {{ ($cliente and $cliente->sentimento == 1) ? 'selected' : '' }}>Positivo</option>
                                <option value="0" {{ ($cliente and $cliente->sentimento == 0) ? 'selected' : '' }}>Neutro</option>
                                <option value="-1" {{ ($cliente and $cliente->sentimento == 11) ? 'selected' : '' }}>Negativo</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <ul class="list-unstyled metadados"></ul>
                    </div>
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
                            <select class="form-control selector-select2" name="cd_estado" id="cd_estado">
                                <option value="">Selecione um estado</option>
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
                            <input type="hidden" name="cd_cidade" id="cd_cidade" value="{{ ($dados->cd_cidade) ? $dados->cd_cidade : 0  }}">
                            <select class="form-control select2" name="cidade" id="cidade" disabled="disabled">
                                <option value="">Selecione uma cidade</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <input type="hidden" name="cd_emissora" id="cd_emissora" value="{{ ($dados->emissora_id) ? $dados->emissora_id : 0  }}">
                            <label>Emissora <span class="text-danger">Obrigatório</span></label>
                            <select class="form-control select2" name="emissora" id="emissora" required>
                            <option value="">Selecione uma emissora</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Horário</label>
                            <input type="text" class="form-control horario" name="horario" id="horario" value="{{ ($dados->horario) ? $dados->horario : ''  }}" placeholder="Horário">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Programa</label>
                            <input type="hidden" name="cd_programa" id="cd_programa" value="{{ ($dados->programa_id) ? $dados->programa_id : 0  }}">
                            <select class="form-control selector-select2" name="programa" id="programa" disabled>
                                <option value="">Selecione um programa</option>
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
<div class="modal fade" id="modalCliente" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h6 style="text-align: left;" class="modal-title" id="exampleModalLabel"><i class="fa fa-envelope"></i> Adicionar Endereço Eletrônico</h6>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Cliente <span class="text-danger">Obrigatório</span></label>
                        <input hidden name="cliente_id" id="cliente_id" value="{{ ($dados and $dados->cliente) ? $dados->cliente->id : '' }}">
                        <select class="form-control cliente select2" name="cliente" id="cliente">
                            <option value="">Selecione um cliente</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Área do Cliente <span class="text-info">Opcional</span></label>
                        <select class="form-control select2" name="area" id="area" disabled>
                            <option value="">Selecione uma área</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Sentimento <span class="text-info">Opcional</span></label>
                        <select class="form-control" name="sentimento" id="sentimento">
                            <option value="">Selecione um sentimento</option>
                            <option value="1">Positivo</option>
                            <option value="0">Neutro</option>
                            <option value="-1">Negativo</option>
                        </select>
                    </div>
                </div>
             
                <div class="col-md-12 center">
                    <div class="form-group mt-3">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>
                        <button type="button" class="btn btn-success btn-add-cliente"><i class="fa fa-plus"></i>Adicionar</button>
                    </div>
                </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('script')    
    <script>
        Dropzone.autoDiscover = false;
        var host = $('meta[name="base-url"]').attr('content');

        $(document).ready(function(){
            
            var token = $('meta[name="csrf-token"]').attr('content');
            var cd_emissora = $("#cd_emissora").val();
            var cliente_id = $("#cliente_id").val();
            
            $(".dropzone").dropzone({ 
                acceptedFiles: ".mp4",
                maxFiles: 1,
                url: host+"/tv/noticias/upload",
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

            $.ajax({
                url: host+'/api/cliente/buscarClientes',
                type: 'GET',
                beforeSend: function() {
                    $('.content').loader('show');
                },
                success: function(data) {
                    if(!data) {
                        Swal.fire({
                            text: 'Não foi possível buscar os clientes. Entre em contato com o suporte.',
                            type: "warning",
                            icon: "warning",
                        });
                        return;
                    }

                    data.forEach(element => {
                        let option = new Option(element.text, element.id);
                        $('.cliente').append(option);
                    });

                },
                complete: function(){
                    if(cliente_id > 0)
                        $('#cd_cliente').val(cliente_id);
                    $('.content').loader('hide');
                }
            });

            $.ajax({
                url: host+'/api/emissora/buscarEmissoras',
                type: 'GET',
                beforeSend: function() {
                    $('.content').loader('show');
                },
                success: function(data) {
                    if(!data) {
                        Swal.fire({
                            text: 'Não foi possível buscar as emissoras. Entre em contato com o suporte.',
                            type: "warning",
                            icon: "warning",
                        });
                        return;
                    }

                    data.forEach(element => {
                        let option = new Option(element.text, element.id);
                        $('#emissora').append(option);
                    });
                },
                complete: function(){
                    if(cd_emissora > 0)
                        $('#emissora').val(cd_emissora);
                    $('.content').loader('hide');
                }
            });

            $(document).on('change', '.cliente', function() {
                var cliente = $(this).val();
                buscarAreas(cliente);
            });

            $(document).on('change', '#emissora', function() {
                
                var emissora = $(this).val();

                buscarProgramas(emissora);


                return $('#programa').prop('disabled', false);
            });

            $(document).on("change", "#horario", function() {
            
                var horario = $(this).val();

                $.ajax({
                    url: host+'/api/programa/buscar-horario/'+horario,
                    type: 'GET',
                    beforeSend: function() {
                        $('.content').loader('show');
                    },
                    success: function(data) {

                        if(data.length > 0) { 

                            $('#programa').find('option').remove();
                            $('#programa').attr('disabled', false);

                            $('#programa').append('<option value="">Selecione um programa</option>').val('');

                            data.forEach(element => {
                                let option = new Option(element.text, element.id);
                                $('#programa').append(option);
                            });
                        }                        
                    },
                    complete: function(){
                        $('.content').loader('hide');
                    }
                });
            });
        });

        $(document).ready(function(){

            var cd_emissora = $("#cd_emissora").val();
            var cd_programa = $("#cd_programa").val();
            var cd_area = $("#area_id").val();
            var cliente_id = $("#cliente_id").val();

            $("#cd_estado").trigger('change');

            if(cd_emissora > 0)
                buscarProgramas(cd_emissora);

            if(cliente_id)
                buscarAreas(cliente_id);
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

                $("#modalCliente").modal('hide');
                
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

        function buscarAreas(cliente){

            var cd_area = $("#area_id").val();

            if(cliente == '') {
                    $('.area').attr('disabled', true);
                    $('.area').append('<option value="">Cliente não possui áreas</option>').val('');
                    return;
                }

                $.ajax({
                    url: host+'/api/cliente/getAreasCliente',
                    type: 'GET',
                    data: {
                        "_token": $('meta[name="csrf-token"]').attr('content'),
                        "cliente": cliente,
                    },
                    beforeSend: function() {
                        $('.content').loader('show');
                        $('.area').append('<option value="">Carregando...</option>').val('');
                    },
                    success: function(data) {

                        $('.area').find('option').remove();
                        $('.area').attr('disabled', false);

                        if(data.length == 0) {                            
                            $('.area').append('<option value="">Cliente não possui áreas vinculadas</option>').val('');
                            return;
                        }
                        
                        $('.area').append('<option value="">Selecione uma área</option>').val('');
                        data.forEach(element => {
                            let option = new Option(element.descricao, element.id);
                            $('.area').append(option);
                        });
                                    
                    },
                    complete: function(){
                        if(cd_area > 0)
                            $('#cd_area').val(cd_area);
                        $('.content').loader('hide');
                    }
                });

        }

        function buscarProgramas(emissora){

            var cd_programa = $("#cd_programa").val();

            $.ajax({
                    url: host+'/api/programa/buscar-emissora/'+emissora,
                    type: 'GET',
                    beforeSend: function() {
                        $('.content').loader('show');
                        $('#programa').append('<option value="">Carregando...</option>').val('');
                    },
                    success: function(data) {

                        $('#programa').find('option').remove();
                        $('#programa').attr('disabled', false);

                        if(data.length == 0) {                            
                            $('#programa').append('<option value="">Emissora não possui programas cadastrados</option>').val('');
                            return;
                        }

                        $('#programa').append('<option value="">Selecione um programa</option>').val('');

                        data.forEach(element => {
                            let option = new Option(element.text, element.id);
                            $('#programa').append(option);
                        });
                        
                    },
                    complete: function(){
                        if(cd_programa > 0)
                            $('#programa').val(cd_programa);
                        $('.content').loader('hide');
                    }
                });

        };

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

    </script>
@endsection