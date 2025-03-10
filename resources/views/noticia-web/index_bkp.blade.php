@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title">
                        <i class="fa fa-globe"></i> Jornal Web 
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
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['buscar-web']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data Inicial</label>
                                        <input type="text" class="form-control datepicker" name="dt_inicial" required="true" value="{{ date("d/m/Y") }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data Final</label>
                                        <input type="text" class="form-control datepicker" name="dt_final" required="true" value="{{ date("d/m/Y") }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-8">
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
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label for="termo" class="form-label">Expressão de Busca <span class="text-primary">Digite o termo ou expressão de busca baseado em regex</span></label>
                                        <textarea class="form-control" name="termo" id="termo" rows="3">{{ $termo }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-12 checkbox-radios mb-0">
                                    <button type="submit" id="btn-find" class="btn btn-primary mb-3"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>     
                        </div>
                    {!! Form::close() !!} 

                    @if($dados->count())
                        <h6 class="px-3">Mostrando {{ $dados->count() }} de {{ $dados->total() }} Notícias</h6>
                    @endif

                    {{ $dados->onEachSide(1)->appends(['dt_inicial' => $dt_inicial, 'dt_final' => $dt_final])->links('vendor.pagination.bootstrap-4') }}    

                    @foreach ($dados as $key => $noticia)
                        <div class="card">
                            <div class="card-body">                           
                                <div class="row">
                                    <div class="col-lg-12 col-sm-12 mb-1">
                                        <p><strong>{{ $noticia->titulo_noticia }}</strong></p>
                                        <div>
                                            @if( \Carbon\Carbon::parse($noticia->data_noticia)->format('d/m/Y') == '01/01/1999')
                                                <span class="badge badge-pill badge-warning"> {{ \Carbon\Carbon::parse($noticia->data_insert)->format('d/m/Y') }}</span>
                                            @else
                                                <span class="badge badge-pill badge-default"> {{ \Carbon\Carbon::parse($noticia->data_noticia)->format('d/m/Y') }}</span>
                                            @endif
                                            {{ ($noticia->fonte) ? $noticia->fonte->nome : 'Fonte desconhecida' }} - Coletada em {{ \Carbon\Carbon::parse($noticia->data_insert)->format('d/m/Y H:i:s') }} 
                                        </div>
                                        
                                        <div class="panel panel-success">
                                            <div class="conteudo-noticia">
                                                {!! ($noticia->conteudo) ?  Str::limit($noticia->conteudo->conteudo, 300, " ...")  : '<span class="text-danger">Nenhum conteúdo coletado</span>' !!}
                                            </div>
                                            <div class="panel-body">
                                                {!! ($noticia->conteudo) ? $noticia->conteudo->conteudo : '<span class="text-danger">Nenhum conteúdo coletado</span>' !!}
                                            </div>
                                            <div class="panel-heading">
                                                <h3 class="panel-title"><span class="btn-show">Mostrar Mais</span></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-8 col-sm-10">
                                        @if($noticia->categoria)
                                            <span class="badge badge-pill badge-primary">{{ $noticia->categoria }}</span>
                                        @else
                                            <span class="badge badge-pill badge-default">Sem Categoria</span>
                                        @endif
                                    </div>
                                    <div class="col-lg-4 col-sm-2">
                                        <a class="btn btn-success btn-sm pull-right" href="{{ asset('noticia/web/detalhes/'.$noticia->id) }}" role="button"><i class="fa fa-eye"> </i> Detalhes</a>
                                        <a class="btn btn-warning btn-sm pull-right" href="{{ $noticia->url_noticia }}" target="_BLANK" role="button"><i class="fa fa-globe"> </i> Ver Original</a>
                                        <a class="btn btn-info btn-sm pull-right" href="{{ asset('noticia/web/estatisticas/'.$noticia->id) }}" role="button"><i class="nc-icon nc-chart-bar-32"> </i> Estatísticas</a>
                                    </div>
                                </div>                               
                            </div>
                            
                        </div>
                    @endforeach

                    {{ $dados->onEachSide(1)->appends(['dt_inicial' => $dt_inicial, 'dt_final' => $dt_final])->links('vendor.pagination.bootstrap-4') }} 
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection
@section('script')
<script>
    $( document ).ready(function() {

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