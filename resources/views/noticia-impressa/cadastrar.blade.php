@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title ml-3">
                        <i class="fa fa-newspaper-o"></i> Impressos 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Cadastrar Notícia
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('impresso') }}" class="btn btn-warning pull-right"><i class="nc-icon nc-chart-pie-36"></i> Dashboard</a>
                    <a href="{{ url('noticias/impresso') }}" class="btn btn-info pull-right"><i class="fa fa-newspaper-o"></i> Listar Notícias</a>
                    <button type="button" class="btn btn-success pull-right btn_enviar_fake" name="btn_enviar_fake" value="salvar"><i class="fa fa-save"></i> Salvar</button>
                    <button type="button" class="btn btn-warning pull-right btn_enviar_copiar_fake" name="btn_enviar_copiar_fake" value="salvar_e_copiar"><i class="fa fa-copy"></i> Salvar e Copiar</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row"> 
                <div class="col-lg-12 col-sm-12">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['noticia-impressa']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <input type="hidden" name="clientes[]" id="clientes">
                                <input type="hidden" name="ds_caminho_img" id="ds_caminho_img">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>Cliente</label>
                                        <select class="form-control cliente select2" name="cd_cliente" id="cd_cliente">
                                            <option value="">Selecione um cliente</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Área do Cliente <span class="text-info add-area" data-toggle="modal" data-target="#modalArea">Adicionar Área</span></label>
                                        <select class="form-control area select2" name="cd_area" id="cd_area" disabled>
                                            <option value="">Selecione uma área</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Sentimento </label>
                                        <select class="form-control" name="cd_sentimento" id="cd_sentimento">
                                            <option value="">Selecione um sentimento</option>
                                            <option value="1">Positivo</option>
                                            <option value="0">Neutro</option>
                                            <option value="-1">Negativo</option>
                                        </select>
                                    </div>                        
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-success btn-add-cliente mt-4 w-100"><i class="fa fa-plus"></i></button>
                                </div>
                                
                                <div class="col-md-12">
                                    <ul class="list-unstyled metadados"></ul>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data de Cadastro</label>
                                        <input type="text" class="form-control datepicker" name="dt_cadastro" readonly required="true" value="{{ date("d/m/Y") }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data do Clipping</label>
                                        <input type="text" class="form-control datepicker" name="dt_clipagem" required="true" value="{{ date("d/m/Y") }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Fonte <span class="text-info add-fonte" data-toggle="modal" data-target="#modalFonte">Adicionar Fonte</span></label>
                                            <select class="form-control select2" name="id_fonte" id="id_fonte" required="true">
                                                <option value="">Selecione uma fonte</option>
                                                @foreach ($fontes as $fonte)
                                                    <option value="{{ $fonte->id }}" {{ (old("id_fonte") ? "selected" : "") }}>{{ $fonte->nome }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Seção <span class="text-primary add-secao" data-toggle="modal" data-target="#addSecao">Adicionar Seção</span></label>
                                        <select class="form-control select2" name="id_sessao_impresso" id="id_sessao_impresso" disabled="true">
                                            <option value="">Selecione uma seção</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Título</label>
                                        <input type="text" class="form-control" name="titulo" id="titulo" minlength="3" placeholder="Título" value="{{ old('titulo') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Estado </label>
                                        <select class="form-control selector-select2" name="cd_estado" id="cd_estado">
                                            <option value="">Selecione um estado</option>
                                            @foreach ($estados as $estado)
                                                <option value="{{ $estado->cd_estado }}" {!! old('cd_estado') == $estado->cd_estado ? " selected" : '' !!}>
                                                    {{ $estado->nm_estado }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Cidade </label>
                                        <input type="hidden" name="cd_cidade" id="cd_cidade" value="{{ (old('cd_cidade')) ? old('cd_cidade') : 0  }}">
                                        <select class="form-control select2" name="cidade" id="cidade" disabled="disabled">
                                            <option value="">Selecione uma cidade</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Página Atual</label>
                                        <input type="text" class="form-control" name="nu_pagina_atual" id="nu_pagina_atual" placeholder="Número">
                                    </div>                                    
                                </div>
                                <div class="col-md-2">
                                    <input type="hidden" name="nu_valor_fonte" id="nu_valor_fonte">
                                    <div class="form-group">
                                        <label>Localização <span class="valor_cm text-info"></span></label>
                                        <select class="form-control" name="local_impressao" id="local_impressao">
                                            <option value="">Selecione um local</option>
                                            <option value="valor_cm_capa_semana" {{ ($fonte->local_impressao == 'valor_cm_capa_semana') ? 'selected' : '' }}>Capa</option>
                                            <option value="valor_cm_capa_fim_semana" {{ ($fonte->local_impressao == 'valor_cm_capa_fim_semana') ? 'selected' : '' }}>Capa FDS</option>
                                            <option value="valor_cm_contracapa" {{ ($fonte->local_impressao == 'valor_cm_contracapa') ? 'selected' : '' }}>Contracapa</option>
                                            <option value="valor_cm_demais_semana" {{ ($fonte->local_impressao == 'valor_cm_demais_semana') ? 'selected' : 'selected' }}>Demais Páginas</option>
                                            <option value="valor_cm_demais_fim_semana" {{ ($fonte->local_impressao == 'valor_cm_demais_fim_semana') ? 'selected' : '' }}>Demais Páginas FDS</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Colunas</label>
                                        <input type="text" class="form-control monetario" name="nu_colunas" id="nu_colunas" placeholder="Colunas" value="{{ old('nu_colunas') }}">
                                    </div>                                    
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Largura <span class="text-info">em cm</span></label>
                                        <input type="text" class="form-control monetario" name="nu_largura" id="nu_largura" placeholder="Largura" value="{{ old('nu_largura') }}">
                                    </div>                                    
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Altura <span class="text-info">em cm</span></label>
                                        <input type="text" class="form-control monetario" name="nu_altura" id="nu_altura" placeholder="Altura" value="{{ old('nu_altura') }}">
                                    </div>                                    
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Retorno</label>
                                        <input type="text" class="form-control retorno_midia" name="valor_retorno" id="valor_retorno" placeholder="Retorno" value="{{ old('valor_retorno') }}">
                                    </div>                                    
                                </div>
                            </div>
                            <div class="row">
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
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="sinopse">Sinopse</label>
                                    <div class="form-group">
                                        <textarea class="form-control" name="sinopse" id="sinopse" rows="10"></textarea>
                                    </div>
                                </div>   

                                <div class="col-md-12">
                                    <label for="arquivo">Print da Notícia</label>
                                    <div style="min-height: 302px;" class="dropzone" id="dropzone"><div class="dz-message" data-dz-message><span>CLIQUE AQUI<br/> ou <br/>ARRASTE</span></div></div>
                                    <input type="hidden" name="arquivo" id="arquivo">
                                </div>                                                         
                            </div>     
                            <div class="text-center mb-2 mt-3">
                                <button type="submit" class="btn btn-success" name="btn_enviar" id="btn_enviar" value="salvar"><i class="fa fa-save"></i> Salvar</button>
                                <button type="submit" class="btn btn-warning" name="btn_enviar" id="btn_enviar_e_salvar" value="salvar_e_copiar"><i class="fa fa-copy"></i> Salvar e Copiar</button>
                                <a href="{{ url('impresso') }}" class="btn btn-danger"><i class="fa fa-times"></i> Cancelar</a>
                            </div>
                        </div>
                    {!! Form::close() !!} 
                </div>
            </div>
        </div>
    </div>
</div> 
<div class="modal fade" id="addSecao" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h6 style="text-align: left;" class="modal-title" id="exampleModalLabel"><i class="fa fa-bookmark "></i> Adicionar Seção</h6>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Nome da Seção</label>
                            <input type="mail" class="form-control" name="ds_sessao" id="ds_sessao">
                        </div>
                    </div>
                </div>
                <div class="center">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>
                    <button type="button" class="btn btn-success btn-salvar-secao"><i class="fa fa-save"></i> Salvar</button>
                </div>
        </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalArea" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h6 style="text-align: left;" class="modal-title" id="exampleModalLabel"><i class="fa fa-tags"></i> Adicionar Área</h6>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Área</label>
                        <input type="text" class="form-control" name="ds_area" id="ds_area" placeholder="Descrição">
                    </div>
                </div>             
            <div class="col-md-12 center">
                <div class="form-group mt-3">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>
                    <button type="button" class="btn btn-success btn-add-area"><i class="fa fa-plus"></i> Adicionar</button>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="modalFonte" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h6 style="text-align: left;" class="modal-title" id="exampleModalLabel"><i class="fa fa-database"></i> Adicionar Fonte</h6>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Fonte</label>
                            <input type="text" class="form-control" name="nome" id="nome" placeholder="Descrição">
                        </div>
                    </div>             
                    <div class="col-md-12 center">
                        <div class="form-group mt-3">
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>
                            <button type="button" class="btn btn-success btn-add-fonte"><i class="fa fa-plus"></i> Adicionar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="{{ asset('js/formulario-cadastro.js') }}"></script>
<script>

    Dropzone.autoDiscover = false;

    $( document ).ready(function() {

        var host = $('meta[name="base-url"]').attr('content');

        $(".btn_enviar_copiar_fake").click(function() {
            $("#btn_enviar_e_salvar").trigger("click");
        });

        $(".btn_enviar_fake").click(function() {
            $("#btn_enviar").trigger("click");
        });

        $("#id_fonte").change(function () {
            $("#local_impressao").trigger('change');
        })

        //Inicializar o Dropzone
        var myDropzone = new Dropzone("#dropzone", {
            url: host + "/noticia-impressa/upload", // URL para onde os arquivos serão enviados
            method: "post", // Método HTTP
            paramName: "picture", // Nome do parâmetro no backend
            maxFilesize: 100, // Tamanho máximo do arquivo em MB
            acceptedFiles: ".jpeg,.jpg,.png,.pdf,.JPG", // Tipos de arquivos aceitos
            addRemoveLinks: true, // Adicionar links para remover arquivos
            dictRemoveFile: "Remover", // Texto do botão de remoção
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"), // Token CSRF para Laravel
            },
            init: function () {
                this.on("success", function (file, response) {
                    $("#ds_caminho_img").val(response);
                });

                this.on("error", function (file, response) {
                    console.error("Erro ao enviar arquivo:", response);
                });

                this.on("removedfile", function (file) {
                    console.log("Arquivo removido:", file.name);
                    // Opcional: envie uma requisição para remover o arquivo do servidor
                });
            },
        });

    

        $(document).on("click", ".btn-add-fonte", function() {

            var nome = $("#nome").val();
          
            $.ajax({url: host+'/fonte-impresso/adicionar',
                type: 'POST',
                data: {"_token": $('meta[name="csrf-token"]').attr('content'),
                        "nome": nome
                },
                beforeSend: function() {
            
                },
                success: function(data) {
                        
                    $.ajax({
                        url: host+'/fonte-impresso/todas',
                        type: 'GET',
                        beforeSend: function() {
                            $('#id_fonte').append('<option value="">Carregando...</option>').val('');
                        },
                        success: function(data) {

                            $('#id_fonte').find('option').remove();                                    
                            $('#id_fonte').append('<option value="">Selecione uma fonte</option>').val('');
                            data.forEach(element => {
                                let option = new Option(element.nome, element.id);
                                $('#id_fonte').append(option);
                            });             
                        },
                        complete: function(){
                                    
                        }
                    });           
                },
                error: function(){
                        
                },
                complete: function(){
                    $('#modalFonte').modal('hide');
                    $("#nome").val("");
                }
            });
        });
    });

    $( document ).ready(function() {
        $("#local_impressao").trigger('change');
    });
</script>
@endsection