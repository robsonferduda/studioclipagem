@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-tv ml-3"></i> TV 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Decupagem 
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('tv/decupagem') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-file-word-o"></i> Decupagem</a>
                    <a href="{{ url('tv/noticias') }}" class="btn btn-info pull-right mr-3"><i class="fa fa-table"></i> Notícias</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            {{ Form::open(array('url' => 'noticia_tv/decupagem/processar', 'method' => 'POST', 'name'=>'product_images')) }}
                <div class="row mr-1 ml-1">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Data <span class="text-danger">Obrigatório</span></label>
                            <input type="text" class="form-control datepicker" name="data" id="data" placeholder="__/__/____" required value="{!! !empty($dados->dt_noticia) ? date('d/m/Y', strtotime($dados->dt_noticia)) : '' !!}">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Emissora <span class="text-danger">Obrigatório</span></label>
                            <select class="form-control select2" name="emissora" id="emissora" required>
                            <option value="">Selecione uma emissora</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Programa <span class="text-danger">Obrigatório</span></label>
                            <select class="form-control selector-select2" name="programa" id="programa" disabled required>
                                <option value="">Selecione um programa</option>
                            </select>
                        </div>
                    </div> 
                    <div class="col-md-12">
                        <label for="arquivo">Arquivo Word <span class="text-info">O arquivo deve conter uma coleção de sinopses, onde cada sinopse é um parágrafo</span></label>
                        <div style="min-height: 100px;" class="dropzone" id="dropzone"><div class="dz-message" data-dz-message><span>CLIQUE AQUI<br/> ou <br/>ARRASTE</span></div></div>
                        <input type="hidden" name="arquivo" id="arquivo">
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-cogs"></i> Processar Documento e Avançar</button>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div> 
@endsection
@section('script')
    <script>

        Dropzone.autoDiscover = false;

        $(document).ready(function() { 

            var token = $('meta[name="csrf-token"]').attr('content');
            var host =  $('meta[name="base-url"]').attr('content');

            $(".dropzone").dropzone({ 
                acceptedFiles: ".doc, .docx",
                init: function() { 
                    myDropzone = this;                   
                },
                maxFiles: 1,
                url: host+"/tv/decupagem/upload",
                headers: {
                    'x-csrf-token': token,
                },
                success: function(file, responseText){
                    $("#arquivo").val(responseText.arquivo);
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
                    $('.content').loader('hide');
                }
            });

            $(document).on('change', '#emissora', function() {
                
                var emissora = $(this).val();

                buscarProgramas(emissora);


                return $('#programa').prop('disabled', false);
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

        });
    </script>
@endsection