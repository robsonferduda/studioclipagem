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
                                        <input type="text" class="form-control datepicker" name="dt_cadastro" required="true" value="{{ date("d/m/Y") }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data do Clipping</label>
                                        <input type="text" class="form-control datepicker" name="dt_clipagem" required="true" value="{{ date("d/m/Y") }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input type="hidden" name="cd_emissora" id="cd_emissora" value="{{ ($noticia and $noticia->emissora_id) ? $noticia->emissora_id : 0  }}">
                                        <label>Emissora <span class="text-danger">Obrigatório</span></label>
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
                                        <label>Programa</label>
                                        <input type="hidden" name="cd_programa" id="cd_programa" value="{{ ($noticia and $noticia->programa_id) ? $noticia->programa_id : 0  }}">
                                        <select class="form-control selector-select2" name="programa" id="programa" disabled>
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
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Duração</label>
                                        <input type="text" class="form-control duracao" name="duracao" id="duracao" placeholder="00:00:00" value="{{ ($noticia) ? $noticia->duracao : '' }}">
                                    </div>
                                </div>                            
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <label>Link</label>
                                        <input type="text" class="form-control" name="link" id="link" placeholder="Link" value="{{ ($noticia) ? $noticia->link : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label for="sinopse">Sinopse</label>
                                    <div class="form-group">
                                        <textarea class="form-control" name="sinopse" id="sinopse" rows="10">{!! ($noticia) ? nl2br($noticia->sinopse) : '' !!}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label for="arquivo">Arquivo</label>
                                    <div style="min-height: 302px;" class="dropzone" id="dropzone"><div class="dz-message" data-dz-message><span>CLIQUE AQUI<br/> ou <br/>ARRASTE</span></div></div>
                                    <input type="hidden" name="arquivo" id="arquivo">
                                </div>
                                <div class="col-md-12 mt-5">

                                    <audio id="audioPlayer" width="100%" controls style="width: 100%;">
                                            <source src="{{ asset('audios/sample-3s.mp3') }}" type="audio/mpeg">
                                            Seu navegador não suporta a execução de áudios, faça o download para poder ouvir.
                                        </audio>


<div class="w3-container">
      <br>
      <div id="waveform" class="w3-border w3-round-large" 
        data-step="3" data-intro="Click and drag to select section">    
      </div>
      <br>
        <div class="w3-row">
        <div class="w3-half w3-container w3-hide" id="audio-buttons">
        <button class="w3-button w3-border w3-border-green w3-round-xlarge" onClick="playAndPause()">
            <i id="play-pause-icon" class="fa fa-play"></i>
        </button>

        <b id="time-current">0.00</b> / <b id="time-total">0.00</b>
        </div>
        
        </div>
      <hr>
          <div data-step="4" data-intro="Would you like to know how to merge tracks. Click Next.">
          <table class="w3-table-all w3-card-4" id="audio-tracks" 
            data-step="5" data-intro="Select atleast 2 checkboxes for merging. Click Next.">
            <thead>
            <tr class="w3-border w3-border-teal w3-text-teal">
              <th></th>
              <th>Início</th>
              <th>Fim</th>
              <th></th>
              <th></th>
              <th></th>
            </tr>
            </thead>
            <tbody></tbody>
            <tfoot></tfoot>
          </table>
          </div>
          <br>
          <div id="merge-option" class="w3-hide">
            <button class="w3-button w3-border w3-border-teal w3-round-xlarge" onClick="mergeTrack()"
                data-step="6" data-intro="Click to merge selected tracks. Bye bye!! :)">
                <i>Merge tracks</i>
            </button>    
            <br><br>
            <div class="w3-row w3-hide" id="merged-track-div">
            <b class="w3-col l1 w3-text-olive"><i>Merged Audio : </i></b>   
            <audio controls="controls" class="w3-col l11" id="merged-track">
                <source src="" type="">
            </audio>
            </div>
          </div>
      <footer class="w3-display-bottom">
        <hr>
        <image id="tour-button" class="w3-right" src="assets/tutorial.png" width="40" height="40" onClick="startTour()" data-step="1" data-intro="Hey User, Welcome. Click me for a walkthrough. To skip click Skip.">
      </footer>
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

<script src="{{ asset('js/formulario-cadastro.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/wavesurfer.js/1.2.3/wavesurfer.min.js"></script>
<!-- wavesurfer.js regions -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/wavesurfer.js/1.2.3/plugin/wavesurfer.regions.min.js"></script>
<!--Enjoy Hints-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/2.9.3/intro.min.js"></script>

<script src="{{ asset('js/actionhelper.js') }}"></script>
<script src="{{ asset('js/audio.js') }}"></script>
<script>
        //Dropzone.autoDiscover = false;
        var host = $('meta[name="base-url"]').attr('content');
        var token = $('meta[name="csrf-token"]').attr('content');

        $(document).ready(function(){


           loadAudio();
            
            var cd_emissora = $("#cd_emissora").val();
            var cliente_id = $("#cliente_id").val();
            /*
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
            });*/

            $(document).on('change', '#emissora_id', function() {
                
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
