@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row ml-1">
                <div class="col-md-6">
                    <h4 class="card-title">
                        <i class="fa fa-globe"></i> Web 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Notícias 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> {!! empty($noticia->id) ? 'Cadastrar' : 'Atualizar' !!} 
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('noticia/web/dashboard') }}" class="btn btn-warning pull-right"><i class="nc-icon nc-chart-pie-36"></i> Dashboard</a>
                    <a href="{{ url('noticia/web') }}" class="btn btn-info pull-right"><i class="fa fa-newspaper-o"></i> Listar Notícias</a>
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
                    @if(empty($noticia))
                        {!! Form::open(['id' => '', 'class' => 'form-horizontal', 'url' => ['noticia-web']]) !!}
                    @else
                        {!! Form::open(['id' => '', 'class' => 'form-horizontal', 'url' => ['noticia-web', $noticia->id], 'method' => 'patch', 'files' => true]) !!}
                    @endif
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <input type="hidden" name="id_noticia" id="id_noticia" value="{{ ($noticia) ? $noticia->id : 0 }}">
                                <input type="hidden" name="clientes[]" id="clientes">
                                <input type="hidden" name="ds_caminho_img" id="ds_caminho_img">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>Cliente</label>
                                        <select class="form-control cliente" name="cd_cliente" id="cd_cliente">
                                            <option value="">Selecione um cliente</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Área do Cliente <span class="text-info add-area" data-toggle="modal" data-target="#modalArea">Adicionar Área</span></label>
                                        <select class="form-control area" name="cd_area" id="cd_area" disabled>
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
                                        <label>Data de Cadastro <span class="text-danger">Obrigatório</span></label>
                                        <input type="text" 
                                        class="form-control datepicker" 
                                        name="data_insert" 
                                        required="true" 
                                        readonly
                                        value="{{ ($noticia and $noticia->data_insert) ? \Carbon\Carbon::parse($noticia->data_insert)->format('d/m/Y') : date("d/m/Y") }}" 
                                        placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data do Clipping <span class="text-danger">Obrigatório</span></label>
                                        <input type="text" 
                                        class="form-control datepicker" 
                                        name="data_noticia" 
                                        required="true" 
                                        value="{{ ($noticia and $noticia->data_noticia) ? \Carbon\Carbon::parse($noticia->data_noticia)->format('d/m/Y') : date("d/m/Y") }}" 
                                        placeholder="__/__/____">
                                    </div>
                                </div>
                               
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Fonte <span class="text-danger">Campo Obrigatório </span>
                                            <a class="text-info" href="{{ url('fonte-web/listar') }}" target="_BLANK">Listagem de Fontes</a>
                                        </label>
                                        <input type="hidden" name="id_fonte" id="id_fonte" value="{{ ($noticia) ? $noticia->id_fonte : '' }}">
                                        <div class="input-group">
                                            <input type="text" class="form-control" style="height: 40px;" id="nome_fonte" placeholder="Selecione uma fonte" value="{{ ($noticia && $noticia->fonte) ? $noticia->fonte->nome : '' }}" readonly required>
                                            <div class="input-group-append">
                                                <button type="button" style="margin: 0px;" class="btn btn-primary" data-toggle="modal" data-target="#modalFonte">Buscar Fonte</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Seção <span class="text-primary add-secao" data-toggle="modal" data-target="#addSecao">Adicionar Seção</span></label>
                                        <select class="form-control select2" name="id_sessao_web" id="id_sessao_web" disabled="true">
                                            <option value="">Selecione uma seção</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Retorno</label>
                                        <input type="text" class="form-control retorno_midia" name="nu_valor" id="nu_valor" placeholder="Retorno" value="{{ ($noticia and $noticia->nu_valor) ? $noticia->nu_valor : old('nu_valor') }}">
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
                                                <option value="{{ $estado->cd_estado }}" {!! ($noticia and $noticia->cd_estado == $estado->cd_estado) ? " selected" : '' !!}>
                                                    {{ $estado->nm_estado }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Cidade </label>
                                        <input type="hidden" name="cd_cidade" id="cd_cidade" value="{{ ($noticia and $noticia->cd_cidade) ? $noticia->cd_cidade : 0  }}">
                                        <select class="form-control select2" name="cd_cidade" id="cidade" disabled="disabled">
                                            <option value="">Selecione uma cidade</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Título <span class="text-danger">Campo Obrigatório</span></label>
                                        <input type="text" class="form-control" name="titulo_noticia" id="titulo_noticia" minlength="3" placeholder="Título" value="{{ (empty($noticia)) ? old('titulo_noticia') : $noticia->titulo_noticia }}" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>URL da Notícia<span class="text-danger"> Campo Obrigatório </span><a class="text-info" href="{{ ($noticia) ? $noticia->url_noticia : '' }}" target="_BLANK">Ver Notícia</a></label>
                                        <input type="text" class="form-control" name="url_noticia" id="url_noticia" placeholder="URL Notícia" value="{{ (empty($noticia)) ? old('url_noticia') : $noticia->url_noticia }}" required>
                                    </div>
                                </div>  
                                 <div class="col-md-12">
                                        <div class="form-group">    
                                            <label for="tags[]">TAGs</label>
                                            <select name="tags[]" multiple="multiple" class="form-control select2">
                                                @foreach ($tags as $tag)
                                                    <option value="{{ $tag->id }}" {{ ($noticia and $noticia->tags->contains($tag->id)) ? 'selected'  : '' }}>{{ $tag->nome }}</option>
                                                @endforeach
                                            </select> 
                                        </div>    
                                    </div> 
                                <div class="col-md-12">
                                    <label for="sinopse">Sinopse</label>
                                    <div class="form-group">
                                        <textarea class="form-control" name="sinopse" id="sinopse" rows="4">{{ (empty($noticia)) ? old('sinopse') : $noticia->sinopse }}</textarea>
                                    </div>
                                </div>  
                                <div class="col-md-12">
                                    <label for="sinopse">Texto <span class="text-danger">Campo Obrigatório</span></label>
                                    <div class="form-group">
                                        <textarea class="form-control" name="conteudo" id="conteudo" rows="10" required>{{ (empty($noticia and $noticia->conteudo)) ? old('conteudo') : $noticia->conteudo->conteudo }}</textarea>
                                    </div>
                                </div> 
                                @if($noticia and $noticia->path_screenshot)    
                                    <div class="col-md-3">
                                        @if($noticia->path_screenshot)                                         
                                            <img src="{{ Storage::disk('s3')->temporaryUrl($noticia->path_screenshot, '+30 minutes') }}" 
                                                alt="Print notícia {{ $noticia->id }}" 
                                                class="img-fluid img-thumbnail" 
                                                style="width: 100%; height: auto; border: none;">
                                        @else
                                            <img src="{{ asset('img/no-print.png') }}" 
                                                alt="Sem Print" 
                                                class="img-fluid img-thumbnail" 
                                                style="width: 100%; height: auto; border: none;">
                                        @endif
                                    </div>
                                    <div class="col-md-9">
                                        <label for="arquivo">Print da Notícia</label>
                                        <div style="min-height: 200px;" class="dropzone" id="dropzone"><div class="dz-message" data-dz-message><span>CLIQUE AQUI<br/> ou <br/>ARRASTE</span></div></div>
                                        <input type="hidden" name="arquivo" id="arquivo">
                                    </div>
                                @else
                                    <div class="col-md-12">
                                        <label for="arquivo">Print da Notícia</label>
                                        <div style="min-height: 200px;" class="dropzone" id="dropzone"><div class="dz-message" data-dz-message><span>CLIQUE AQUI<br/> ou <br/>ARRASTE</span></div></div>
                                        <input type="hidden" name="arquivo" id="arquivo">
                                    </div> 
                                @endif                                                 
                            </div>     
                            <div class="text-center mb-2 mt-3">
                                <button type="submit" class="btn btn-success" name="btn_enviar" id="btn_enviar" value="salvar"><i class="fa fa-save"></i> Salvar</button>
                                <button type="submit" class="btn btn-warning" name="btn_enviar" id="btn_enviar_e_salvar" value="salvar_e_copiar"><i class="fa fa-copy"></i> Salvar e Copiar</button>
                                <a href="{{ url('noticia/web') }}" class="btn btn-danger"><i class="fa fa-times"></i> Cancelar</a>
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
<div class="modal fade" id="modalFonte" tabindex="-1" role="dialog" aria-labelledby="modalFonteLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-scrollable modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header" style="padding: 15px !important;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h6 style="text-align: left;" class="modal-title" id="exampleModalLabel"><i class="fa fa-newspaper-o"></i><span></span> SELEÇÃO DE FONTES</h6>
        </div>
        <div class="modal-body" style="padding: 15px;">
            <form id="formBuscaFonte" class="form-inline mb-3">
                <div class="row">
                    <div class="col-md-12">
                        <input type="text" class="form-control mr-2 mb-2" id="filtro_nome" placeholder="Nome da Fonte" style="width: 35%;">
                        <select class="form-control mr-2 mb-2" name="cd_estado" id="filtro_estado" style="width: 30%;">
                            <option value="">Selecione um estado</option>
                            @foreach ($estados as $estado)
                                <option value="{{ $estado->cd_estado }}" {!! ($noticia and $noticia->cd_estado == $estado->cd_estado) ? " selected" : '' !!}>
                                    {{ $estado->nm_estado }}
                                </option>
                            @endforeach
                        </select>
                        <select class="form-control mr-2 mb-2" name="cd_cidade" id="filtro_cidade" style="width: 30%;" disabled="disabled">
                            <option value="">Selecione uma cidade</option>
                        </select>
                    </div>
                    <div class="col-md-12 center">
                        <button type="button" class="btn btn-info mb-2" id="btnBuscarFonte"><i class="fa fa-search"></i> Buscar</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>
                    </div>
                </div>
            </form>
        <div id="resultadoFontes">
          <!-- Resultados AJAX aqui -->
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
@section('script')
<script src="{{ asset('js/formulario-cadastro-web.js') }}"></script>
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

        //Inicializar o Dropzone
        var myDropzone = new Dropzone("#dropzone", {
            url: host + "/noticia-web/upload", // URL para onde os arquivos serão enviados
            method: "post", // Método HTTP
            paramName: "picture", // Nome do parâmetro no backend
            maxFilesize: 10, // Tamanho máximo do arquivo em MB
            acceptedFiles: ".jpeg,.jpg,.png,.pdf", // Tipos de arquivos aceitos
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

        // Cálculo do valor de retorno
        $(document).on("change", "#id_fonte", function() {
           
            var id = $("#id_fonte").val();
            
            $.ajax({
                    url: host+'/fonte-web/'+id+'/valores/'+$(this).val(),
                    type: 'GET',
                    beforeSend: function() {
                        
                    },
                    success: function(data) {
                        $("#nu_valor").val(data);                                      
                    },
                    complete: function(){
                                    
                    }
            });  
        });

        var id_fonte = $("#id_fonte").val();

        if(id_fonte){
            $("#id_fonte").trigger("change");
        }

        function buscarFontes(pagina = 1) {
            var nome = $('#filtro_nome').val();
            var estado = $('#filtro_estado').val();
            var cidade = $('#filtro_cidade').val();

            // Limpa o resultado antes de buscar
            $('#resultadoFontes').html('');

            $.ajax({
                url: '{{ url("fonte-web/buscar/combo") }}',
                type: 'GET',
                data: {
                    nome: nome,
                    estado: estado,
                    cidade: cidade,
                    page: pagina
                },
                success: function(res) {
                    var html = '<table class="table table-bordered"><tr><th>Nome</th><th>Estado</th><th>Cidade</th><th>Ação</th></tr>';
                    if(res.data.length == 0) {
                        html += '<tr><td colspan="4">Nenhuma fonte encontrada.</td></tr>';
                    } else {
                        $.each(res.data, function(i, fonte) {
                            html += '<tr>';
                            html += '<td>' + fonte.nome + '</td>';
                            html += '<td>' + (fonte.estado || '') + '</td>';
                            html += '<td>' + (fonte.cidade || '') + '</td>';
                            html += '<td><button type="button" class="btn btn-success btn-sm selecionar-fonte" data-id="'+fonte.id+'" data-nome="'+fonte.nome+'">Selecionar</button></td>';
                            html += '</tr>';
                        });
                    }
                    html += '</table>';

                     // Paginação customizada
                    var current = res.current_page;
                    var last = res.last_page;
                    var start = Math.max(1, current - 5);
                    var end = Math.min(last, start + 9);
                    start = Math.max(1, end - 9); // Garante que sempre mostre até 10 páginas

                    html += '<nav><ul class="pagination justify-content-center">';

                    // Botão anterior
                    if(current > 1) {
                        html += '<li class="page-item"><a class="page-link paginacao-fonte" href="#" data-pagina="'+(current-1)+'">&laquo; Anterior</a></li>';
                    } else {
                        html += '<li class="page-item disabled"><span class="page-link">&laquo; Anterior</span></li>';
                    }

                    // Números das páginas
                    for(var i = start; i <= end; i++) {
                        html += '<li class="page-item '+(i==current?'active':'')+'"><a class="page-link paginacao-fonte" href="#" data-pagina="'+i+'">'+i+'</a></li>';
                    }

                    // Botão próxima
                    if(current < last) {
                        html += '<li class="page-item"><a class="page-link paginacao-fonte" href="#" data-pagina="'+(current+1)+'">Próxima &raquo;</a></li>';
                    } else {
                        html += '<li class="page-item disabled"><span class="page-link">Próxima &raquo;</span></li>';
                    }

                    html += '</ul></nav>';

                    $('#resultadoFontes').html(html);
                }
            });
        }

        $('#btnBuscarFonte').on('click', function() {
            buscarFontes(1);
        });

        $(document).on('keypress',function(e) {
            if(e.which == 13) {
                $('#btnBuscarFonte').trigger('click');
                return false; // Previne o comportamento padrão do Enter
            }
        });

        // Paginação
        $(document).on('click', '.paginacao-fonte', function(e) {
            e.preventDefault();
            var pagina = $(this).data('pagina');
            buscarFontes(pagina);
        });

        // Selecionar fonte
        $(document).on('click', '.selecionar-fonte', function() {
            var id = $(this).data('id');
            var nome = $(this).data('nome');
            $('#id_fonte').val(id);
            $('#nome_fonte').val(nome);
            $('#modalFonte').modal('hide');
            // Se quiser disparar o evento de change para buscar valor_retorno:
            $('#id_fonte').trigger('change');
        });

    });
</script>
@endsection