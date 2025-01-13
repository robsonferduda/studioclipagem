@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row ml-1">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-tv ml-3"></i> TV
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Vídeos
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('tv/emissoras') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-tv"></i> Emissoras</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="col-md-12">
                {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['tv/videos']]) !!}
                    <div class="form-group m-3 w-70">
                        <div class="row mb-0">
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>Data Inicial</label>
                                    <input type="text" class="form-control datepicker dt-search" name="dt_inicial" id="dt_inicial" required="true" value="{{ ($dt_inicial) ? date('d/m/Y', strtotime($dt_inicial)) : date('d/m/Y') }}" placeholder="__/__/____">
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>Data Final</label>
                                    <input type="text" class="form-control datepicker dt-search" name="dt_final" required="true" value="{{ ($dt_final) ? date('d/m/Y', strtotime($dt_final)) : date('d/m/Y') }}" placeholder="__/__/____">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Emissora</label>
                                    <select class="form-control select2" name="fonte" id="fonte">
                                        <option value="">Selecione uma emissora</option>
                                        @foreach ($emissoras as $emissora)
                                            <option value="{{ $emissora->id }}" {{ ($emissora->id == $fonte) ? 'selected' : '' }}>{{ $emissora->nome_emissora }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>   
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Programa</label>
                                    <input type="hidden" name="cd_programa_selecionado" id="cd_programa_selecionado" value="{{ $programa }}">
                                    <select class="form-control select2" name="programa" id="programa" disabled>
                                        <option value="">Selecione um programa</option>
                                        @foreach ($programas as $prog)
                                            <option value="{{ $prog->id }}" {{ ($prog->id == $programa) ? 'selected' : '' }}>{{ $prog->nome_programa }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>                             
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="expressao" class="form-label">Expressão de Busca <span class="text-primary">Digite o termo ou expressão de busca baseado em regex</span></label>
                                    <textarea class="form-control" name="expressao" id="expressao" rows="3">{{ $expressao }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" id="btn-find" class="btn btn-primary mt-4 btn-search"><i class="fa fa-search"></i> Buscar</button>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
            <div class="col-md-12">
                @if(count($videos) > 0)
                    <h6 class="px-3">Mostrando {{ $videos->count() }} de {{ $videos->total() }} vídeos coletados</h6>
                    {{ $videos->onEachSide(1)->appends(['dt_inicial' => $dt_inicial, 'dt_final' => $dt_final, 'fonte' => $fonte, 'programa' => $programa, 'expressao' => $expressao ])->links('vendor.pagination.bootstrap-4') }}
                @endif

                @if(count($videos) > 0)
                    @foreach ($videos as $key => $video)
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4 col-sm-12">                                            
                                        <video width="100%" height="240" controls>
                                            <source src="{{ (Storage::disk('s3')) ? Storage::disk('s3')->temporaryUrl($video->video_path, '+30 minutes') : '' }}" type="video/mp4">
                                            <source src="movie.ogg" type="video/ogg">
                                            Seu navegador não suporta a exibição de vídeos.
                                        </video>
                                    </div>
                                    <div class="col-lg-8 col-sm-12">                                        
                                        <p class="mb-1">
                                            @if($video->programa and $video->programa->tipo and in_array($video->programa->tipo->id, [4,5]))
                                                <i class="fa fa-youtube text-danger" aria-hidden="true" style="font-size: 30px;"></i>
                                            @endif
                                            <strong>{{ ($video->programa and $video->programa->emissora) ? $video->programa->emissora->nome_emissora : '' }}</strong> - 
                                            <strong>{{ ($video->programa) ? $video->programa->nome_programa : '' }}</strong>
                                        </p>
                                        <p class="mb-1">
                                            @if($video->programa and $video->programa->tipo and $video->misc_data and $video->programa->tipo and in_array($video->programa->tipo->id, [4,5]) and !$video->horario_start_gravacao)
                                                @php 
                                                    $partes = explode(',', explode(')',explode('(', $video->misc_data)[1])[0]);
                                                    $data = $partes[2].'/'.$partes[1].'/'.$partes[0];                                                    
                                                @endphp
                                                {{ $data }}
                                            @else
                                            {{ date('d/m/Y', strtotime($video->horario_start_gravacao)) }}
                                            @endif
                                             - Das 
                                            {{ date('H:i:s', strtotime($video->horario_start_gravacao)) }} às 
                                            {{ date('H:i:s', strtotime($video->horario_end_gravacao)) }}
                                        </p>

                                        <div class="panel panel-success">
                                            <div class="conteudo-noticia mb-1 transcricao">
                                                {!! ($video->transcricao) ?  Str::limit($video->transcricao, 700, " ...")  : '<span class="text-danger">Nenhum conteúdo coletado</span>' !!}
                                            </div>
                                            <div class="panel-body transcricao-total">
                                                {!! ($video->transcricao) ?  $video->transcricao  : '<span class="text-danger">Nenhum conteúdo coletado</span>' !!}
                                            </div>
                                            <div class="panel-heading">
                                                <h3 class="panel-title"><span class="btn-show">Mostrar Mais</span></h3>
                                            </div>
                                        </div> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')    
    <script>
      
        var host = $('meta[name="base-url"]').attr('content');

        $(document).ready(function(){ 

            $(".panel-heading").click(function() {
                $(this).parent().addClass('active').find('.panel-body').slideToggle('fast');
                $(".panel-heading").not(this).parent().removeClass('active').find('.panel-body').slideUp('fast');
            });

            $(".btn-show").click(function(){

                var texto = $(this).text();

                if(texto == 'Mostrar Mais'){

                    $(this).closest('.panel').find('.conteudo-noticia').addClass('d-none');
                    $(this).html("Mostrar Menos");                   

                }
                
                if(texto == 'Mostrar Menos'){

                    $(this).closest('.panel').find('.conteudo-noticia').removeClass('d-none');
                    $(this).html("Mostrar Mais");
                }

                destacaTexto();

            });

            destacaTexto();

            function destacaTexto(){

                var expressao = "{{ $expressao }}";
                var context = document.querySelector("body");
                var instance_ods = new Mark(context);
                
                var options = {"element": "mark",
                            "separateWordSearch": false,
                            "accuracy": {
                                    "value": "exactly",
                                    "limiters": [",", "."]
                                },
                                "diacritics": true
                            };

                instance_ods.mark(expressao, options); 
            }

            var emissora = $("#fonte").val();

            if(emissora){
                buscarProgramas(emissora);
            }

            $(document).on('change', '#fonte', function() {
                
                var emissora = $(this).val();

                buscarProgramas(emissora);

                return $('#programa').prop('disabled', false);
            });

            function buscarProgramas(emissora){

                var cd_programa = $("#cd_programa_selecionado").val();

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
        });
    </script>
@endsection