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
                    <a href="{{ url('noticia/web/dashboard') }}" class="btn btn-warning pull-right mr-3"><i class="nc-icon nc-chart-pie-36"></i> Dashboard</a>
                    <a href="{{ url('noticia/web') }}" class="btn btn-info pull-right mr-3"><i class="fa fa-newspaper-o"></i> Listar Notícias</a>
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
                                        <label>Data Clipagem <span class="text-danger">Campo Obrigatório</span></label>
                                        <input type="text" class="form-control datepicker" name="data_insert" required="true" value="{{ date("d/m/Y") }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data Noticia <span class="text-danger">Campo Obrigatório</span></label>
                                        <input type="text" class="form-control datepicker" name="data_noticia" required="true" value="{{ date("d/m/Y") }}" placeholder="__/__/____">
                                    </div>
                                </div>
                               
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Fonte <span class="text-danger">Campo Obrigatório</span></label>
                                        <select class="form-control" name="id_fonte" id="id_fonte" required="required">
                                            <option value="">Selecione uma fonte</option>
                                            @foreach ($fontes as $fonte)
                                                <option value="{{ $fonte->id }}" {{ (empty($noticia)) ? ((old('id_fonte') == $fonte->id) ? 'selected' : '') : (($noticia->id_fonte == $fonte->id) ? 'selected' : '')  }}>{{ $fonte->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Retorno</label>
                                        <input type="text" class="form-control monetario" name="valor_retorno" id="valor_retorno" placeholder="Retorno" value="{{ old('valor_retorno') }}" readonly>
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
                                        <label>URL da Notícia <span class="text-danger">Campo Obrigatório</span></label>
                                        <input type="text" class="form-control" name="url_noticia" id="url_noticia" placeholder="URL Notícia" value="{{ (empty($noticia)) ? old('url_noticia') : $noticia->url_noticia }}" required>
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
                                        <textarea class="form-control" name="conteudo" id="conteudo" rows="10" required>{{ (empty($noticia)) ? old('conteudo') : $noticia->conteudo->conteudo }}</textarea>
                                    </div>
                                </div>     
                                <div class="col-md-12">
                                    <label for="arquivo">Print da Notícia</label>
                                    <div style="min-height: 200px;" class="dropzone" id="dropzone"><div class="dz-message" data-dz-message><span>CLIQUE AQUI<br/> ou <br/>ARRASTE</span></div></div>
                                    <input type="hidden" name="arquivo" id="arquivo">
                                </div>                                                         
                            </div>     
                            <div class="text-center mb-2 mt-3">
                                <button type="submit" class="btn btn-success" name="btn_enviar" value="salvar"><i class="fa fa-save"></i> Salvar</button>
                                <button type="submit" class="btn btn-warning" name="btn_enviar" value="salvar_e_copiar"><i class="fa fa-copy"></i> Salvar e Copiar</button>
                                <a href="{{ url('noticia/web') }}" class="btn btn-danger"><i class="fa fa-times"></i> Cancelar</a>
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
<script src="{{ asset('js/formulario-cadastro.js') }}"></script>
<script>

    Dropzone.autoDiscover = false;

    $( document ).ready(function() {

        var host = $('meta[name="base-url"]').attr('content');

        //Inicializar o Dropzone
        var myDropzone = new Dropzone("#dropzone", {
            url: host + "/noticia-web/upload", // URL para onde os arquivos serão enviados
            method: "post", // Método HTTP
            paramName: "picture", // Nome do parâmetro no backend
            maxFilesize: 1, // Tamanho máximo do arquivo em MB
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
    });
</script>
@endsection