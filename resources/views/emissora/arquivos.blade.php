@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title ml-3">
                        <i class="fa fa-volume-up"></i> Rádio
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Arquivos
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Listar
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('radio/dashboard') }}" class="btn btn-warning pull-right mr-3"><i class="nc-icon nc-chart-pie-36"></i> Dashboard</a>
                    <a href="{{ url('emissoras/radio') }}" class="btn btn-primary pull-right mr-3"><i class="fa fa-volume-up"></i> Emissoras de Rádio</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['radio/arquivos']]) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data Inicial</label>
                                        <input type="text" class="form-control datepicker" name="dt_inicial" required="true" value="{{ date('d/m/Y') }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data Final</label>
                                        <input type="text" class="form-control datepicker" name="dt_final" required="true" value="{{ date('d/m/Y') }}" placeholder="__/__/____">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <label>Fontes</label>
                                    <div class="form-group">
                                        <select multiple="multiple" size="10" name="fontes[]" id="fontes" class="demo1 form-control">
                                            @foreach ($fontes as $fonte)
                                                <option value="{{ $fonte->id }}" {{ (Session::get('radio_filtro_fonte') and in_array($fonte->id, Session::get('radio_filtro_fonte'))) ? 'selected' : '' }}>{{ $fonte->nome_emissora }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label for="expressao" class="form-label">Expressão de Busca <span class="text-primary">Digite o termo ou expressão de busca</span></label>
                                        <textarea class="form-control" name="expressao" id="expressao" rows="3">{{ $expressao }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-12 checkbox-radios mb-0">
                                    <button type="submit" id="btn-find" class="btn btn-primary mb-3"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                    {!! Form::close() !!}

                    @if(count($dados) > 0)
                        <h6 class="px-3">Mostrando {{ $dados->count() }} de {{ $dados->total() }} Páginas</h6>

                        {{ $dados->onEachSide(1)->appends(['dt_inicial' => $dt_inicial, 'dt_final' => $dt_final, 'fonte' => $fonte, 'expressao' => $expressao])->links('vendor.pagination.bootstrap-4') }}
                    @endif

                    @foreach ($dados as $key => $audio)
                        <div class="card">
                            <div class="card-body">                    
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 mb-1">                                    
                                        @if(Storage::disk('s3')->temporaryUrl($audio->path_s3, '+30 minutes'))
                                            <audio width="100%" controls style="width: 100%;">
                                                <source src="{{ Storage::disk('s3')->temporaryUrl($audio->path_s3, '+30 minutes') }}" type="audio/mpeg">
                                                Seu navegador não suporta a execução de áudios, faça o download para poder ouvir.
                                            </audio>
                                        @else
    
                                        @endif
                                    </div>
                                    <div class="col-lg-12 col-sm-12 mb-1"> 
                                        <h6><a href="{{ url('emissora/'.$audio->id_fonte.'/edit') }}" target="_BLANK">{{ ($audio->nome_fonte) ? $audio->nome_fonte : '' }}</a></h6>  
                                        <h6 style="color: #FF5722;">{{ ($audio->nm_estado) ? $audio->nm_estado : '' }}{{ ($audio->nm_cidade) ? "/".$audio->nm_cidade : '' }}</h6>  
                                        <h6 class="text-muted mb-1">
                                            {{ ($audio->nome_fonte) ? $audio->nome_fonte : '' }} - 
                                            {{ \Carbon\Carbon::parse($audio->data_hora_inicio)->format('d/m/Y') }} - 
                                            De {{ \Carbon\Carbon::parse($audio->data_hora_inicio)->format('H:i:s') }} às {{ \Carbon\Carbon::parse($audio->data_hora_fim)->format('H:i:s') }}
                                        </h6> 
                                        <p class="mb-2"><strong>Retorno de Mídia</strong>: {!! ($audio->valor_retorno) ? "R$ ".$audio->valor_retorno : '<span class="text-danger">Não calculado</span>' !!}</p>
                                        <div>
                                            <a href="{{ url('jornal-impresso/noticia/extrair/web',$audio->id) }}" class="btn btn-success btn-sm"><i class="fa fa-database"></i> Extrair Notícia</a> 
                                        </div>
                                        <div class="panel panel-success">
                                            <div class="conteudo-noticia mb-1">
                                                {!! ($audio->transcricao) ?  Str::limit($audio->transcricao, 1000, " ...")  : '<span class="text-danger">Nenhum conteúdo coletado</span>' !!}
                                            </div>
                                            <div class="panel-body conteudo-{{ $audio->id }}">
                                                {!! ($audio->transcricao) ?  $audio->transcricao  : '<span class="text-danger">Nenhum conteúdo coletado</span>' !!}
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')    
    <script>
      
        $(document).ready(function(){ 

            var host =  $('meta[name="base-url"]').attr('content');

        var demo2 = $('.demo1').bootstrapDualListbox({
            nonSelectedListLabel: 'Disponíveis',
            selectedListLabel: 'Selecionadas',               
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
        });
    </script>
@endsection