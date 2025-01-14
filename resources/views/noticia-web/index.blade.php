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
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('fonte-web/listar') }}" class="btn btn-info pull-right mr-3"><i class="fa fa-database"></i> Fontes Web</a>
                    <a href="{{ url('noticia/web/cadastrar') }}" class="btn btn-primary pull-right mr-3"><i class="fa fa-plus"></i> Cadastrar Notícia</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row mb-0">
                <div class="col-lg-12 col-sm-12 mb-0">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['buscar-web']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data Inicial</label>
                                        <input type="text" class="form-control datepicker" name="dt_inicial" id="dt_inicial" required="true" value="{{ date("d/m/Y") }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data Final</label>
                                        <input type="text" class="form-control datepicker" name="dt_final" id="dt_final" required="true" value="{{ date("d/m/Y") }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Fonte</label>
                                        <select class="form-control select2" name="fonte" id="fonte">
                                            <option value="">Selecione uma fonte</option>
                                            @foreach ($fontes as $f)
                                                <option value="{{ $f->id }}" {{ ($fonte == $f->id ) ? 'selected' : '' }}>{{ $f->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Clientes</label>
                                        <select class="form-control select2" name="cliente" id="cliente">
                                            <option value="">Selecione um cliente</option>
                                            @foreach ($clientes as $cli)
                                                <option value="{{ $cli->id }}" {{ ($cliente == $cli->id ) ? 'selected' : '' }}>{{ $cli->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="form-check">
                                            <div class="form-check">
                                                <label class="form-check-label" style="margin-top: 15px;">
                                                    <input class="form-check-input" {{ (true) ? 'checked' : '' }} type="checkbox" name="fl_print" value="true">
                                                        NOTÍCIAS COM PRINT
                                                    <span class="form-check-sign"></span>
                                                </label>
                                            </div>
                                        </div>
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
                                <div class="col-md-12 checkbox-radios mb-0">
                                    <button type="submit" id="btn-find" class="btn btn-primary mb-3"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>     
                        </div>
                    {!! Form::close() !!} 
                </div>
                <div class="col-lg-12 col-sm-12 conteudo">      
                    @if(count($dados))
                        <h6 class="px-3">Mostrando {{ $dados->count() }} de {{ $dados->total() }} notícias</h6> 
                        {{ $dados->onEachSide(1)->appends(['fl_print' => $fl_print, 'expressao' => $expressao])->links('vendor.pagination.bootstrap-4') }}
                    @endif
                </div>
                <div class="col-lg-12">
                    @if(count($dados) > 0)
                        @foreach ($dados as $key => $dado)
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-2 col-sm-12">   
                                            @if($dado->noticia->path_screenshot)                                         
                                                <img src="{{ Storage::disk('s3')->temporaryUrl($dado->noticia->path_screenshot, '+2 minutes') }}" alt="Print">
                                            @endif
                                        </div>
                                        <div class="col-lg-10 col-sm-12">                                        
                                            <div class="conteudo-noticia mb-1">
                                                <p class="font-weight-bold mb-1">{{ $dado->noticia->titulo_noticia }}</p>
                                                <p class="text-muted"> {!! ($dado->noticia->data_noticia) ? date('d/m/Y', strtotime($dado->noticia->data_noticia)) : date('d/m/Y', strtotime($dado->noticia->data_noticia)) !!} - {{ $dado->noticia->fonte->nome }}</p> 
                                            </div>
                                            <div class="panel panel-success">
                                                <div class="conteudo-noticia mb-1 transcricao">
                                                    {!! ($dado->conteudo) ?  Str::limit($dado->conteudo, 700, " ...")  : '<span class="text-danger">Nenhum conteúdo coletado</span>' !!}
                                                </div>
                                                <div class="panel-body transcricao-total">
                                                    {!! ($dado->conteudo) ?  $dado->conteudo  : '<span class="text-danger">Nenhum conteúdo coletado</span>' !!}
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
                <div class="col-lg-12 col-sm-12 conteudo">      
                    @if(count($dados))
                        {{ $dados->onEachSide(1)->appends(['fl_print' => $fl_print, 'expressao' => $expressao])->links('vendor.pagination.bootstrap-4') }}
                    @endif
                </div>
            </div>
            <div class="row mt-0">
                <div class="col col-sm-12 col-md-12 col-lg-12">
                    <div class="load-busca" style="min-height: 200px;" >
                        <h6 class="label-resultado ml-3">Resultados da Busca</h6>
                        <div class="resultados m-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection
@section('script')
<script>
    $( document ).ready(function() {

        var host =  $('meta[name="base-url"]').attr('content');
        var token = $('meta[name="csrf-token"]').attr('content');

        $(".periodo").change(function(){
            var periodo = $(this).val();
            inicializaDatas(periodo);        
        });

        function inicializaDatas(periodo)
        {
            var dataFinal = new Date();
            var dataInicial = new Date();    
                
            if(periodo != 'personalizado'){
                dataInicial.setDate(dataFinal.getDate() - (periodo - 1));
            }else{
                $(".dt_inicial_relatorio").focus();
            }

            $(".dt_inicial_relatorio").val(formataData(dataInicial));
            $(".dt_final_relatorio").val(formataData(dataFinal));

            $(".label_data_inicial").html(formataData(dataInicial));
            $(".label_data_final").html(formataData(dataFinal));   
        }

        function formataData(data)
        {
            var dia = String(data.getDate()).padStart(2, '0');
            var mes = String(data.getMonth() + 1).padStart(2, '0');
            var ano = data.getFullYear();

            return dia + '/' + mes + '/' + ano;
        }

        $("#btn-find").click(function(){

        /*
            var expressao = $("#expressao").val();
            var dt_inicial = $("#dt_inicial").val();
            var dt_final = $("#dt_final").val();
            var fonte = $("#fonte").val();

            var ajaxTime = new Date().getTime();

            $.ajax({url: host+'/buscar-web',
                    type: 'POST',
                    data: {"_token": $('meta[name="csrf-token"]').attr('content'),
                            "dt_inicial": dt_inicial,
                            "dt_final": dt_final,
                            "fonte": fonte,
                            "expressao": expressao
                    },
                    beforeSend: function() {
                        $('.load-busca').loader('show');
                    },
                    success: function(data) {

                        $(".label-resultado").css("display","block");
                        $(".resultados").empty();

                        var totalTime = millisToMinutesAndSeconds(new Date().getTime() - ajaxTime);

                        if(data.length == 0){

                            $(".resultados").append('<span class="text-danger">Consulta realizada em <strong>'+totalTime+'</strong> não encontrou nenhum registro</span>');

                        }else{

                            $(".resultados").append('<span class="mb-3">Consulta realizada em <strong>'+totalTime+'</strong> encontrou '+data.length+' registros</span><br/><br/>');

                            $.each(data, function(k, v) {
                            // $(".resultados").append('<p><a href="'+v.url_noticia+'" target="BLANK">'+v.titulo_noticia+'</a></p>');
                                $(".resultados").append('<div><p class="fts_detalhes" style="font-weight: 600;" data-chave="txt-'+k+'" data-id="'+v.id+'">'+v.titulo+'</p><div id="txt-'+k+'"></div></div>');
                            });
                        }                            
                    },
                    error: function(){
                        $(".resultados").empty();
                        $(".resultados").append('<span class="text-danger">Erro ao executar o string de busca</span>');
                    },
                    complete: function(){
                        $('.load-busca').loader('hide');
                    }
            });
            */

        });

        function millisToMinutesAndSeconds(millis) {
            var minutes = Math.floor(millis / 60000);
            var seconds = ((millis % 60000) / 1000).toFixed(0);
            return minutes + ":" + (seconds < 10 ? '0' : '') + seconds;
        }

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

        });

    });
</script>
@endsection