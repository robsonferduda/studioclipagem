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
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="card-title ml-2"><i class="fa fa-volume-up"></i> Rádio
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Notícias
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> {!! empty($noticia->id) ? 'Cadastrar' : 'Atualizar' !!}</h4>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ url('radio/dashboard') }}" class="btn btn-warning pull-right mr-3"><i class="nc-icon nc-chart-pie-36"></i> Dashboard</a>
                        <a href="{{ url('noticias/radio') }}" class="btn btn-info pull-right mr-3"><i class="fa fa-newspaper-o"></i> Listar Notícias</a>
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
                            {!! Form::open(['id' => 'frm_noticia_radio_criar', 'url' => ['noticia-radio'], 'method' => 'post', 'files' => true]) !!}
                        @else
                            {!! Form::open(['id' => 'frm_noticia_radio_editar', 'url' => ['noticia-radio', $noticia->id], 'method' => 'patch', 'files' => true]) !!}
                        @endif
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <input type="hidden" name="id_noticia" id="id_noticia" value="{{ ($noticia) ? $noticia->id : 0 }}">
                                <input type="hidden" name="clientes[]" id="clientes">
                                <input type="hidden" name="ds_caminho_audio" id="ds_caminho_audio" value="{{ ($noticia and $noticia->ds_caminho_audio) ? $noticia->ds_caminho_audio : '' }}">
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
                                        <label>Data de Cadastro</label>
                                        <input type="text" class="form-control datepicker" name="dt_cadastro" readonly required="true" 
                                        value="{{ ($noticia and $noticia->dt_cadastro) ? \Carbon\Carbon::parse($noticia->dt_cadastro)->format('d/m/Y') : date("d/m/Y") }}" 
                                        placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data do Clipping</label>
                                        <input type="text" class="form-control datepicker" name="dt_clipagem" required="true" 
                                        value="{{ ($noticia and $noticia->dt_clipagem) ? \Carbon\Carbon::parse($noticia->dt_clipagem)->format('d/m/Y') : date("d/m/Y") }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input type="hidden" name="cd_emissora" id="cd_emissora" value="{{ ($noticia and $noticia->emissora_id) ? $noticia->emissora_id : 0  }}">
                                        <label>Emissora <span class="text-danger">Obrigatório </span><span class="text-info" id="valor_segundo" data-valor=""></span></label>
                                        <select class="form-control select2" name="emissora_id" id="emissora_id" required="true">
                                            <option value="">Selecione uma emissora</option>
                                            @foreach ($emissoras as $emissora)
                                                <option value="{{ $emissora->id }}" {!! ($noticia and $noticia->emissora_id == $emissora->id) ? "selected" : '' !!}>
                                                    {{ $emissora->nome_emissora }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Programa <span class="text-info" id="programa_valor_segundo"></span></label>
                                        <input type="hidden" name="cd_programa" id="cd_programa" value="{{ ($noticia and $noticia->programa_id) ? $noticia->programa_id : 0  }}">
                                        <select class="form-control selector-select2" name="programa_id" id="programa" disabled>
                                            <option value="">Selecione um programa</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Horário</label>
                                        <input type="text" class="form-control horario" name="horario" id="horario" value="{{ ($noticia and $noticia->horario) ? $noticia->horario : ''  }}" placeholder="Horário">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                 <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Retorno</label>
                                        <input type="text" class="form-control retorno_midia" name="valor_retorno" id="valor_retorno" placeholder="Retorno" value="{{ ($noticia and $noticia->valor_retorno) ? $noticia->valor_retorno : old('valor_retorno') }}">
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
                                        <input type="hidden" name="cd_cidade" id="cd_cidade_selecionada" value="{{ ($noticia and $noticia->cd_cidade) ? $noticia->cd_cidade : 0  }}">
                                        <select class="form-control select2" name="cd_cidade" id="cidade" disabled="disabled">
                                            <option value="">Selecione uma cidade</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
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
                            </div>
                            <div class="row">                          
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Link</label>
                                        <input type="text" class="form-control" name="link" id="link" placeholder="Link" value="{{ ($noticia) ? $noticia->link : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label for="sinopse">Sinopse</label>
                                    <div class="form-group">
                                        <textarea class="form-control" name="sinopse" id="sinopse" rows="4">{!! ($noticia) ? nl2br($noticia->sinopse) : '' !!}</textarea>
                                    </div>
                                </div>
                                @if($noticia and $noticia->ds_caminho_audio)    
                                    <div class="col-md-12">
                                        <audio width="100%" controls style="width: 100%;">
                                            <source src="{{ asset('audio/noticia-radio/'.$noticia->ds_caminho_audio) }}" type="audio/mpeg">
                                                Seu navegador não suporta a execução de áudios, faça o download para poder ouvir.
                                        </audio>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="arquivo">Áudio da Notícia</label>
                                        <div style="min-height: 200px;" class="dropzone" id="dropzone"><div class="dz-message" data-dz-message><span>CLIQUE AQUI<br/> ou <br/>ARRASTE</span></div></div>
                                        <input type="hidden" name="arquivo" id="arquivo">
                                    </div>
                                @else
                                    <div class="col-md-12">
                                        <p class="text-danger mb-1">A notícia não possui nenhum arquivo de áudio vinculado</p>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="arquivo">Áudio da Notícia</label>
                                        <div style="min-height: 200px;" class="dropzone" id="dropzone"><div class="dz-message" data-dz-message><span>CLIQUE AQUI<br/> ou <br/>ARRASTE</span></div></div>
                                        <input type="hidden" name="arquivo" id="arquivo">
                                    </div> 
                                @endif 
                                <div class="col-md-12 mt-2">
                                    <div class="form-group">
                                        <label>Duração <span class="text-danger">Será preenchida automaticamente ao fazer o upload. Caso falhe, insira manualmente.</span></label>
                                        <input type="text" class="form-control duracao" name="duracao" id="duracao" placeholder="00:00:00" value="{{ ($noticia) ? $noticia->duracao : '' }}">
                                    </div>
                                </div>                                    
                            </div>
                             
                            <div class="text-center mb-2 mt-3">
                                <button type="submit" class="btn btn-success" name="btn_enviar" value="salvar"><i class="fa fa-save"></i> Salvar</button>
                                <button type="submit" class="btn btn-warning" name="btn_enviar" value="salvar_e_copiar"><i class="fa fa-copy"></i> Salvar e Copiar</button>
                                <a href="{{ url('noticias/radio') }}" class="btn btn-danger"><i class="fa fa-times"></i> Cancelar</a>
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
<script src="{{ asset('js/formulario-cadastro-radio.js') }}"></script>
<script>
    
    Dropzone.autoDiscover = false;
    var host = $('meta[name="base-url"]').attr('content');
    var token = $('meta[name="csrf-token"]').attr('content');
    
    $(document).ready(function(){

        var cd_emissora = $("#cd_emissora").val();

        //Inicializar o Dropzone
        var myDropzone = new Dropzone("#dropzone", {
            url: host + "/noticia-radio/upload", // URL para onde os arquivos serão enviados
            method: "post", // Método HTTP
            paramName: "audio", // Nome do parâmetro no backend
            maxFilesize: 100, // Tamanho máximo do arquivo em MB
            acceptedFiles: ".mp3", // Tipos de arquivos aceitos
            addRemoveLinks: true, // Adicionar links para remover arquivos
            dictRemoveFile: "Remover", // Texto do botão de remoção
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"), // Token CSRF para Laravel
            },
            init: function () {
                this.on("success", function (file, response) {
                    $("#ds_caminho_audio").val(response.arquivo);
                    $("#duracao").val(response.duracao);
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

        $(document).on('change', '#emissora_id', function() {
                
            var emissora = $(this).val();

            // Busca valor do segundo da emissora
            if(emissora) {
                $.ajax({
                    url: host + '/emissora/radio/' + emissora + '/segundo',
                    type: 'GET',
                    success: function(data) {
                        // Exemplo: preenche um campo com id="valor_segundo"
                        if(data.valor_segundo){
                            $('#valor_segundo').text('R$ '+data.valor_segundo);
                            $('#valor_segundo').attr('data-valor', data.valor_segundo);
                            calculaTempo(data.valor_segundo);
                        }
                        else
                            $('#valor_segundo').html('<span class="text-warning">Valor Pendente</span>');
                    }
                });

                $.ajax({
                    url: host + '/emissora/radio/' + emissora,
                    type: 'GET',
                    success: function(data) {
                        
                        if(data.cd_estado){
                            $("#cd_estado").val(data.cd_estado);
                        }else{
                            $("#cd_estado").val('');
                        }

                        if(data.cd_cidade){                            
                            $("#cd_cidade_selecionada").val(data.cd_cidade).change();
                            $("#cd_estado").trigger('change'); 
                        }else{
                            $("#cd_cidade_selecionada").val('');
                            $("#cd_estado").trigger('change'); 
                        }                       
                    }
                });


            } else {
                $('#valor_segundo').text('');
            }
            
            buscarProgramas(emissora);
            return $('#programa').prop('disabled', false);
        });

        $(document).on('change', '#programa', function() {

            var programa_id = $(this).val();
            if(programa_id) {
                $.ajax({
                    url: host + '/radio/programa/' + programa_id + '/dados',
                    type: 'GET',
                    success: function(data) {
                        // Preencha os campos desejados
                        $('#horario').val(data.horario);
                        if(data.valor)
                            $('#programa_valor_segundo').text('R$ ' + data.valor);
                        else
                            $('#programa_valor_segundo').html('<span class="text-warning">Valor Pendente</span>');
                    }
                });
            } else {
                $('#horario').val('');
                $('#programa_valor_segundo').text('');
            }
        });

        $(document).on('change', '#duracao', function() {

            var valor = $('#valor_segundo').data("valor");
            calculaTempo(valor);

        });

        function calculaTempo(valor){

            let tempo = $('#duracao').val();
            let valorPorSegundo = parseFloat(valor);

            if(tempo){
                if (!tempo.match(/^\d{2}:\d{2}:\d{2}$/)) {
                  alert("Formato de tempo inválido. Use HH:MM:SS.");
                  return;
                }

                let partes = tempo.split(':');
                let horas = parseInt(partes[0], 10);
                let minutos = parseInt(partes[1], 10);
                let segundos = parseInt(partes[2], 10);

                let totalSegundos = (horas * 3600) + (minutos * 60) + segundos;

                let valorTotal = totalSegundos * valorPorSegundo;

                $('#valor_retorno').val(valorTotal.toFixed(2));
            }
        }


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
            $("#emissora_id").trigger('change');
           
        });
        

        $(document).on("click", ".selecionar-arquivo", function() {
            $('#arquivo').trigger('click');
        });

    
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
