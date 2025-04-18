@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title ml-3">
                        <i class="fa fa-newspaper-o"></i> Impressos 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Cadastrar Notícia
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('impresso') }}" class="btn btn-warning pull-right mr-3"><i class="nc-icon nc-chart-pie-36"></i> Dashboard</a>
                    <a href="{{ url('noticias/impresso') }}" class="btn btn-info pull-right mr-3"><i class="fa fa-newspaper-o"></i> Listar Notícias</a>
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
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data de Cadastro</label>
                                        <input type="text" class="form-control datepicker" name="dt_cadastro" required="true" value="{{ date("d/m/Y") }}" placeholder="__/__/____">
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
                                            <label>Fonte</label>
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
                                        <label>Seção</label>
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
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Total de Páginas</label>
                                        <input type="text" class="form-control" name="nu_paginas_total" id="nu_paginas_total" placeholder="Número">
                                    </div>                                    
                                </div>
                                <div class="col-md-8 col-sm-12">
                                    <div class="form-group">
                                        <label>Link</label>
                                        <input type="text" class="form-control" name="ds_link" id="ds_link" placeholder="URL">
                                    </div>                                    
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label>Colunas</label>
                                        <input type="text" class="form-control monetario" name="nu_colunas" id="nu_colunas" placeholder="Colunas" value="{{ old('nu_colunas') }}">
                                    </div>                                    
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label>Altura</label>
                                        <input type="text" class="form-control monetario" name="nu_altura" id="nu_altura" placeholder="Altura" value="{{ old('nu_altura') }}">
                                    </div>                                    
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label>Largura</label>
                                        <input type="text" class="form-control monetario" name="nu_largura" id="nu_largura" placeholder="Largura" value="{{ old('nu_largura') }}">
                                    </div>                                    
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label>Retorno</label>
                                        <input type="text" class="form-control monetario" name="valor_retorno" id="valor_retorno" placeholder="Retorno" value="{{ old('valor_retorno') }}" readonly>
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
                                <button type="submit" class="btn btn-success" name="btn_enviar" value="salvar"><i class="fa fa-save"></i> Salvar</button>
                                <button type="submit" class="btn btn-warning" name="btn_enviar" value="salvar_e_copiar"><i class="fa fa-copy"></i> Salvar e Copiar</button>
                                <a href="{{ url('impresso') }}" class="btn btn-danger"><i class="fa fa-times"></i> Cancelar</a>
                            </div>
                        </div>
                    {!! Form::close() !!} 
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection
@section('script')
<script>

    Dropzone.autoDiscover = false;

    $( document ).ready(function() {

        var host = $('meta[name="base-url"]').attr('content');

        //Inicializar o Dropzone
        var myDropzone = new Dropzone("#dropzone", {
            url: host + "/upload", // URL para onde os arquivos serão enviados
            method: "post", // Método HTTP
            paramName: "file", // Nome do parâmetro no backend
            maxFilesize: 1, // Tamanho máximo do arquivo em MB
            acceptedFiles: ".jpeg,.jpg,.png,.pdf", // Tipos de arquivos aceitos
            addRemoveLinks: true, // Adicionar links para remover arquivos
            dictRemoveFile: "Remover", // Texto do botão de remoção
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"), // Token CSRF para Laravel
            },
            init: function () {
                this.on("success", function (file, response) {
                    console.log("Arquivo enviado com sucesso:", response);
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

        $(document).on('change', '.monetario', function() {
                
            var retorno = 0;
            var altura = ($("#nu_altura").val()) ? $("#nu_altura").val() : 1;
            var largura = ($("#nu_largura").val()) ? $("#nu_largura").val() : 1;
            var colunas = ($("#nu_colunas").val()) ? $("#nu_colunas").val() : 1;

            retorno = altura * largura * colunas;

            // Truncar o valor com duas casas decimais
            retorno = retorno.toFixed(2);

            $("#valor_retorno").val(retorno);
        });


        $(document).on('change', '#id_fonte', function() {
                
                var fonte = $(this).val();

                buscarSecoes(fonte);

                return $('#id_sessao_impresso').prop('disabled', false);
            });


        function buscarSecoes(id_fonte){

            //var cd_programa = $("#cd_programa").val();

            $.ajax({
                    url: host+'/noticia/impresso/fonte/sessoes/'+id_fonte,
                    type: 'GET',
                    beforeSend: function() {
                        $('.content').loader('show');
                        $('#id_sessao_impresso').append('<option value="">Buscando seções...</option>').val('');
                    },
                    success: function(data) {

                        $('#id_sessao_impresso').find('option').remove();
                        $('#id_sessao_impresso').attr('disabled', false);

                        if(data.length == 0) {                            
                            $('#id_sessao_impresso').append('<option value="">Fonte não possui seções cadastradas</option>').val('');
                            return;
                        }

                        $('#id_sessao_impresso').append('<option value="">Selecione uma seção</option>').val('');

                        data.forEach(element => {
                            let option = new Option(element.ds_sessao, element.id_sessao_impresso);
                            $('#id_sessao_impresso').append(option);
                        });
                        
                    },
                    complete: function(){
                        /*
                        if(cd_programa > 0)
                            $('#programa').val(cd_programa);
                        */
                        $('.content').loader('hide');
                    }
                });

        };

    });
</script>
@endsection