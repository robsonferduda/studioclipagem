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
                    <a href="{{ url('noticia/web/dashboard') }}" class="btn btn-warning pull-right mr-3"><i class="nc-icon nc-chart-pie-36"></i> Dashboard</a>
                    <a href="{{ url('noticia/web/novo') }}" class="btn btn-primary pull-right mr-3"><i class="fa fa-newspaper-o"></i> Novo</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row mb-0">
                <div class="col-lg-12 col-sm-12 mb-0">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['noticia/web/monitoramento']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Tipo de Data</label>
                                        <select class="form-control select2" name="tipo_data" id="tipo_data">
                                            <option value="data_insert" {{ ($tipo_data == "data_insert") ? 'selected' : '' }}>Data de Cadastro</option>
                                            <option value="data_noticia" {{ ($tipo_data == "data_noticia") ? 'selected' : '' }}>Data do Clipping</option>
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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Cliente</label>
                                        <select class="form-control select2" name="cliente" id="cliente">
                                            <option value="">Selecione um cliente</option>
                                            @foreach ($clientes as $cliente)
                                                <option value="{{ $cliente->id }}" {{ ($cliente_selecionado == $cliente->id) ? 'selected' : '' }}>{{ $cliente->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Monitoramento</label>
                                    <input type="hidden" name="monitoramento_id" id="monitoramento_id" value="{{ Session::get('web_monitoramento') }}">
                                    <div class="form-group">
                                        <select class="form-control" name="monitoramento" id="monitoramento" disabled>
                                            <option value="">Selecione um monitoramento</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <label>Fontes</label>
                                    <div class="form-group">
                                        <select multiple="multiple" size="10" name="fontes[]" class="demo1 form-control">
                                            @foreach ($fontes as $fonte)
                                                <option value="{{ $fonte->id }}">{{ $fonte->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Buscar por <span class="text-primary">Digite o termo ou expressão de busca</span></label>
                                        <input type="text" class="form-control" name="termo" id="termo" minlength="3" placeholder="Termo" value="{{ $termo }}">
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
                        
                        {{ $dados->onEachSide(1)->appends(['dt_inicial' => \Carbon\Carbon::parse($dt_inicial)->format('d/m/Y'), 
                                                            'dt_final' => \Carbon\Carbon::parse($dt_final)->format('d/m/Y'),
                                                            'cliente' => $cliente_selecionado,
                                                            'termo' => $termo])
                                                            ->links('vendor.pagination.bootstrap-4') }}
                    @endif
                </div>
                <div class="col-lg-12">
                    @if(count($dados) > 0)
                        @foreach ($dados as $key => $dado)
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-2 col-sm-12">   
                                            @if($dado->path_screenshot)                                         
                                                <img src="" alt="Print">
                                            @endif
                                        </div>
                                        <div class="col-lg-10 col-sm-12"> 
                                            <div class="conteudo-{{ $dado->noticia_id }}">
                                                <p class="font-weight-bold mb-1">{{ $dado->titulo_noticia }}</p>
                                                <h6><a href="{{ url('fonte-web/editar', $dado->id_fonte) }}" target="_BLANK">{{ ($dado->nome_fonte) ? $dado->nome_fonte : '' }}</a></h6>  
                                                <h6 style="color: #FF5722;">{{ ($dado->nm_estado) ? $dado->nm_estado : '' }}{{ ($dado->nm_cidade) ? "/".$dado->nm_cidade : '' }}</h6> 
                                                <p class="text-muted"> {!! ($dado->data_noticia) ? date('d/m/Y', strtotime($dado->data_noticia)) : date('d/m/Y', strtotime($dado->data_noticia)) !!} - {{ $dado->nome_fonte }}</p> 
                                                <p class="mb-1"><i class="nc-icon nc-briefcase-24"></i> {{ ($dado->nome_cliente) ? $dado->nome_cliente : '' }}</p>
                                                <div style="margin-bottom: 5px;" class="tags destaque-{{ $dado->noticia_id }}-{{ $dado->monitoramento_id }}" data-monitoramento="{{ $dado->monitoramento_id }}" data-chave="{{ $dado->noticia_id }}-{{ $dado->monitoramento_id }}" data-noticia="{{ $dado->noticia_id }}">
                                                
                                                </div>
                                                <code>
                                                    <a href="{{ url('monitoramento/'.$dado->monitoramento_id.'/editar') }}" target="_BLANK">{{ $dado->expressao }}</a>
                                                </code>
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
                    {{ $dados->onEachSide(1)->appends(['dt_inicial' => \Carbon\Carbon::parse($dt_inicial)->format('d/m/Y'), 
                                                        'dt_final' => \Carbon\Carbon::parse($dt_final)->format('d/m/Y'),
                                                        'cliente' => $cliente_selecionado,
                                                        'termo' => $termo])
                                                        ->links('vendor.pagination.bootstrap-4') }}
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

        });

        $("#cliente").change(function(){

                var cliente_selecionado = $(this).val();

                if(cliente_selecionado){

                    $.ajax({
                        url: host+'/monitoramento/cliente/'+cliente_selecionado+'/fl_web',
                        type: 'GET',
                        beforeSend: function() {
                            $('#monitoramento').find('option').remove().end();
                            $('#monitoramento').append('<option value="">Carregando...</option>').val('');                            
                        },
                        success: function(data) {
                            $('#monitoramento').attr('disabled', false);
                            $('#monitoramento').find('option').remove().end();

                            $('#monitoramento').append('<option value="" selected>Selecione um monitoramento</option>').val(''); 
                            data.forEach(element => {

                                var nome = (element.nome) ? element.nome : 'Monitoramento sem nome';

                                let option = new Option(nome, element.id);
                                $('#monitoramento').append(option);
                            });    
                            
                            var monitoramento_selecionado = $("#monitoramento_id").val();
                            if(monitoramento_selecionado > 0){
                                if($("#monitoramento option[value="+monitoramento_selecionado+"]").length > 0)
                                    $("#monitoramento").val(monitoramento_selecionado);
                            }
                        },
                        error: function(){
                            $('#monitoramento').find('option').remove().end();
                            $('#monitoramento').append('<option value="">Erro ao carregar dados...</option>').val('');
                        },
                        complete: function(){
                                
                        }
                    }); 

                }
             
            });

            $("#monitoramento").change(function(){

                var monitoramento_selecionado = $(this).val();

                if(monitoramento_selecionado){

                    $.ajax({
                        url: host+'/monitoramento/'+monitoramento_selecionado+'/fontes',
                        type: 'GET',
                        beforeSend: function() {
                                                       
                        },
                        success: function(data) {
                            if(data.filtro_web){

                                const lista_fontes = JSON.parse("[" + data.filtro_web + "]");

                                console.log(lista_fontes);

                                
                                for (var i = 0; i < $('#fontes option').length; i++) {
                                    if ($('#fontes option')[i].value == 1) {
                                        
                                    }
                                }
                            }
                            
                        },
                        error: function(){
                           
                        },
                        complete: function(){
                                
                        }
                    }); 

                }

            });

             $(".tags").each(function() {
               
                var monitoramento = $(this).data("monitoramento");
                var noticia = $(this).data("noticia");
                var chave = ".destaque-"+$(this).data("chave");
                var chave_conteudo = ".conteudo-"+$(this).data("chave");

                $.ajax({
                    url: host+'/web/conteudo/'+noticia+'/monitoramento/'+monitoramento,
                    type: 'GET',
                    beforeSend: function() {
                            
                    },
                    success: function(data) {
                        
                        $(chave_conteudo).html(data.texto);

                        var marks = [];                 
                        
                        const divContent = document.querySelector(chave_conteudo);

                        if (divContent) {
            
                            const childElements = divContent.querySelectorAll('mark');
                            const output = document.querySelector(chave);

                            childElements.forEach(element => {

                                if(!marks.includes(element.innerHTML.trim())){
                                    marks.push(element.innerHTML.trim());

                                    $(chave).append('<span class="destaque-busca">'+element.innerHTML.trim()+'</span>');
                                }
                            });
                        } 
                    },
                    complete: function(){
                            
                    }
                });
            });

    });
    $(document).ready(function(){
        $('#cliente').trigger('change');
    });
</script>
@endsection