@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row ml-1">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-tv ml-3"></i> TV
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Notícias
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Coletas
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
                {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['noticia/tv/coletas']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Tipo de Data</label>
                                        <select class="form-control select2" name="tipo_data" id="tipo_data">
                                            <option value="created_at" {{ ($tipo_data == "created_at") ? 'selected' : '' }}>Data de Cadastro</option>
                                            <option value="dt_pub" {{ ($tipo_data == "dt_pub") ? 'selected' : '' }}>Data do Clipping</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data Inicial</label>
                                        <input type="text" class="form-control datepicker" name="dt_inicial" required="true" value="{{ \Carbon\Carbon::parse($dt_inicial)->format('d/m/Y') }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data Final</label>
                                        <input type="text" class="form-control datepicker" name="dt_final" required="true" value="{{ \Carbon\Carbon::parse($dt_final)->format('d/m/Y') }}" placeholder="__/__/____">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <label>Fontes</label>
                                    <div class="form-group">
                                        <select multiple="multiple" size="10" name="fontes[]" class="demo1 form-control">
                                            @foreach ($fontes as $fonte)
                                                <option value="{{ $fonte['id'] }}" {{ $fonte['flag'] }}>{{ $fonte['estado']."-" }}  {{ $fonte['nome'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Buscar por <span class="text-primary">Digite o termo ou expressão de busca</span></label>
                                        <input type="text" class="form-control" name="expressao" id="expressao" minlength="3" placeholder="Expressão" value="{{ $expressao }}">
                                    </div>
                                </div>
                                <div class="col-md-12 checkbox-radios mb-0">
                                    <button type="submit" id="btn-find" class="btn btn-primary mb-3"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                {!! Form::close() !!}
            </div>
            <div class="col-md-12">
                @if(count($videos) > 0)
                    <h6 class="px-3">Mostrando {{ $videos->count() }} de {{ $videos->total() }} vídeos coletados</h6>

                     {{ $videos->onEachSide(1)->appends(['dt_inicial' => \Carbon\Carbon::parse($dt_inicial)->format('d/m/Y'), 
                                                        'dt_final' => \Carbon\Carbon::parse($dt_final)->format('d/m/Y'),
                                                        'expressao' => $expressao])
                                                        ->links('vendor.pagination.bootstrap-4') }}
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
                                            @if($video->tipo_programa and in_array($video->tipo_programa, [4,5]))
                                                <i class="fa fa-youtube text-danger" aria-hidden="true" style="font-size: 30px;"></i>
                                            @endif
                                            <strong>{{ ($video->nome_emissora) ? $video->nome_emissora : '' }}</strong> - 
                                            <strong>{{ ($video->nome_programa) ? $video->nome_programa : '' }}</strong>
                                        </p>
                                        <p class="mb-1">
                                            @if($video->tipo_programa and in_array($video->tipo_programa, [4,5]) and !$video->horario_start_gravacao)
                                                @php 
                                                    $partes = explode(',', explode(')',explode('(', $video->misc_data)[1])[0]);
                                                    $data = str_pad($partes[2],2,"0",STR_PAD_LEFT).'/'.str_pad($partes[1],2,"0",STR_PAD_LEFT).'/'.$partes[0];                                                    
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
                                                {!! ($video->transcricao) ?  Str::limit($video->transcricao, 1000, " ...")  : '<span class="text-danger">Nenhum conteúdo coletado</span>' !!}
                                            </div>
                                            <div class="panel-body conteudo-{{ $video->noticia_id }}">
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

                      {{ $videos->onEachSide(1)->appends(['dt_inicial' => \Carbon\Carbon::parse($dt_inicial)->format('d/m/Y'), 
                                                        'dt_final' => \Carbon\Carbon::parse($dt_final)->format('d/m/Y'),
                                                        'expressao' => $expressao])
                                                        ->links('vendor.pagination.bootstrap-4') }}
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')    
    <script>
        $(document).ready(function(){ 

            var host = $('meta[name="base-url"]').attr('content');

            var demo2 = $('.demo1').bootstrapDualListbox({
                nonSelectedListLabel: 'Disponíveis',
                selectedListLabel: 'Selecionadas',
               
            });

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