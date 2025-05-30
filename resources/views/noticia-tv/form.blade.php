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
                    <h4 class="card-title ml-2"><i class="fa fa-tv"></i> TV
                    <i class="fa fa-angle-double-right" aria-hidden="true"></i> Notícias
                    <i class="fa fa-angle-double-right" aria-hidden="true"></i> {!! empty($dados->id) ? 'Cadastrar' : 'Atualizar' !!}</h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('tv/dashboard') }}" class="btn btn-warning pull-right mr-3"><i class="nc-icon nc-chart-pie-36"></i> Dashboard</a>
                    <a href="{{ url('noticias/tv') }}" class="btn btn-info pull-right mr-3"><i class="fa fa-tv"></i> Notícias</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    <div class="form-group m-3 w-70">
                        @if(empty($dados->id))
                            {!! Form::open(['id' => 'frm_noticia_radio_criar', 'url' => ['tv/noticias/inserir'], 'method' => 'post', 'files' => true]) !!}
                        @else
                            {!! Form::open(['id' => 'frm_noticia_radio_editar', 'url' => ['tv/noticias/'. $dados->id. '/atualizar'], 'method' => 'post', 'files' => true]) !!}
                        @endif
                        <div class="row">
                                <input type="hidden" name="id_noticia" id="id_noticia" value="{{ ($dados) ? $dados->id : 0 }}">
                                <input type="hidden" name="clientes[]" id="clientes">
                                <input type="hidden" name="ds_caminho_video" id="ds_caminho_video" value="{{ ($dados and $dados->ds_caminho_video) ? $dados->ds_caminho_video : '' }}">
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
                                    <input type="hidden" name="cd_emissora" id="cd_emissora" value="{{ ($dados->emissora_id) ? $dados->emissora_id : 0  }}">
                                    <label>Emissora <span class="text-danger">Obrigatório </span><a class="text-info" href="{{ url("tv/emissoras") }}" target="_BLANK">Listar Emissoras</a></label>
                                    <select class="form-control select2" name="emissora" id="emissora" required>
                                    <option value="">Selecione uma emissora</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Programa</label>
                                    <input type="hidden" name="cd_programa" id="cd_programa" value="{{ ($dados->programa_id) ? $dados->programa_id : 0  }}">
                                    <select class="form-control selector-select2" name="programa" id="programa" disabled>
                                        <option value="">Selecione um programa</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Horário</label>
                                    <input type="text" class="form-control horario" name="horario" id="horario" value="{{ ($dados->horario) ? $dados->horario : ''  }}" placeholder="Horário">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label>Retorno</label>
                                    <input type="text" class="form-control retorno_midia" name="valor_retorno" id="valor_retorno" placeholder="Retorno" value="{{ old('valor_retorno') }}">
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
                                            <option value="{{ $estado->cd_estado }}" {!! $dados->cd_estado == $estado->cd_estado ? " selected" : '' !!}>
                                                {{ $estado->nm_estado }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Cidade </label>
                                    <input type="hidden" name="cd_cidade" id="cd_cidade" value="{{ ($dados->cd_cidade) ? $dados->cd_cidade : 0  }}">
                                    <select class="form-control select2" name="cidade" id="cidade" disabled="disabled">
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
                                            <option value="{{ $tag->id }}">{{ $tag->nome }}</option>
                                        @endforeach
                                    </select> 
                                </div>    
                            </div> 
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Link</label>
                                    <input type="text" class="form-control" name="link" id="link" placeholder="Link" value="{{ $dados->link }}">
                                </div>
                            </div> 
                            <div class="col-md-12">
                                <label for="sinopse">Sinopse</label>
                                <div class="form-group">
                                    <textarea class="form-control" name="sinopse" id="sinopse" rows="10">{!! nl2br($dados->sinopse) !!}</textarea>
                                </div>
                            </div>
                            @if($dados and $dados->ds_caminho_video)    
                                <div class="col-md-4">
                                    <video width="100%" height="240" controls>
                                        <source src="{{ asset('video/noticia-tv/'.$dados->ds_caminho_video) }}" type="video/mp4">
                                        <source src="movie.ogg" type="video/ogg">
                                                Seu navegador não suporta a exibição de vídeos.
                                    </video>
                                </div>
                                <div class="col-md-8">
                                    <label for="arquivo">Vídeo da Notícia</label>
                                    <div style="min-height: 200px;" class="dropzone" id="dropzone"><div class="dz-message" data-dz-message><span>CLIQUE AQUI<br/> ou <br/>ARRASTE</span></div></div>
                                    <input type="hidden" name="arquivo" id="arquivo">
                                </div>
                            @else
                                <div class="col-md-12">
                                    <label for="arquivo">Vídeo da Notícia</label>
                                    <div style="min-height: 200px;" class="dropzone" id="dropzone"><div class="dz-message" data-dz-message><span>CLIQUE AQUI<br/> ou <br/>ARRASTE</span></div></div>
                                    <input type="hidden" name="arquivo" id="arquivo">
                                </div> 
                            @endif 
                        </div>
                        <div class="row"> 
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Duração <span class="text-danger">Obrigatório</span></label>
                                    <input type="text" class="form-control duracao" name="duracao" id="duracao" placeholder="00:00:00" value="{{ $dados->duracao }}">
                                </div>
                            </div>
                        </div>
                        <div class="text-center mb-2">
                            <button type="submit" class="btn btn-success" name="btn_enviar" value="salvar"><i class="fa fa-save"></i> Salvar</button>
                            <button type="submit" class="btn btn-warning" name="btn_enviar" value="salvar_e_copiar"><i class="fa fa-copy"></i> Salvar e Copiar</button>
                            <a href="{{ url('noticias/tv') }}" class="btn btn-danger"><i class="fa fa-times"></i> Cancelar</a>
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
<script src="{{ asset('js/formulario-cadastro-tv.js') }}"></script> 
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
                success: function(file, response){

                    $("#ds_caminho_video").val(response.arquivo);
                    $("#duracao").focus();
                }
            });

            $.ajax({
                url: host+'/api/tv/emissora/buscar',
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

        function buscarProgramas(emissora){

            var cd_programa = $("#cd_programa").val();

            $.ajax({
                    url: host+'/api/tv/emissora/'+emissora+'/programas/buscar',
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
    </script>
@endsection