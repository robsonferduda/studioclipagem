@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title">
                        <i class="fa fa-tv ml-3"></i> TV
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Notícias
                    </h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('tv/dashboard') }}" class="btn btn-warning pull-right mr-3"><i class="nc-icon nc-chart-pie-36"></i> Dashboard</a>
                    <a href="{{ url('tv/noticias/cadastrar') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-plus"></i> Cadastrar Notícia</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="col-md-12">
                {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['tv/noticias']]) !!}
                    <div class="form-group w-70">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Tipo de Data</label>
                                    <select class="form-control select2" name="regra" id="regra">
                                        <option value="dt_noticia">Data de Cadastro</option>
                                        <option value="dt_clipagem">Data do Clipping</option>
                                    </select>
                                </div>
                            </div>
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Cliente</label>
                                    <select class="form-control select2" name="regra" id="regra">
                                        <option value="">Selecione um cliente</option>
                                        @foreach ($clientes as $cliente)
                                            <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Emissora</label>
                                    <select class="form-control select2" name="fonte" id="fonte">
                                        <option value="">Selecione uma emissora</option>
                                        @foreach ($emissoras as $emissora)
                                            <option value="{{ $emissora->id }}">{{ $emissora->nome_emissora }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label>Buscar por <span class="text-primary">Digite o termo ou expressão de busca</span></label>
                                    <input type="text" class="form-control" name="termo" id="termo" minlength="3" placeholder="Termo" value="">
                                </div>
                            </div>
                            <div class="col-md-12 checkbox-radios mb-0">
                                <button type="submit" id="btn-find" class="btn btn-primary mb-3"><i class="fa fa-search"></i> Buscar</button>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}

                    @if(count($noticias))
                        <h6 class="px-3">Mostrando {{ $noticias->count() }} de {{ $noticias->total() }} Notícias</h6>
                        {{ $noticias->onEachSide(1)->links('vendor.pagination.bootstrap-4') }} 
                    @endif 
            </div>
            <div class="col-md-12">
                @foreach($noticias as $noticia)
                    <div class="card ml-2 mr-2">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <span class="dt_noticia_box">{!! !empty($noticia->dt_noticia) ? date('d/m/Y', strtotime($noticia->dt_noticia)) : '' !!}</span>
                                    <h6>{!! ($noticia->cliente) ? $noticia->cliente->nome : 'Nenhum cliente vinculado' !!}</h6>
                                    <p>{!! $noticia->emissora->nome_emissora ?? '' !!} {{ ($noticia->horario) ? ' - '.$noticia->horario : "" }}</p>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12">  
                                    <div class="row">  
                                        <div class="col-lg-3 col-md-3 col-sm-12">                               
                                            <video width="100%" height="auto" controls>
                                                <source src="https://docmidia-files.s3.us-east-1.amazonaws.com/app/files/streams/479516717_20241108_163711.mp4" type="video/mp4">
                                                <source src="https://docmidia-files.s3.us-east-1.amazonaws.com/app/files/streams/479516717_20241108_163711.mp4" type="video/ogg">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                        <div class="col-lg-9 col-md-9 col-sm-12">
                                            <div style="margin-bottom: 15px;">
                                                {!! ($noticia->transcricao) ?  Str::limit($noticia->transcricao, 700, " ...")  : '<span class="text-danger">Nenhum conteúdo coletado</span>' !!}
                                            </div>
                                            <div style="position: absolute; bottom: 15px; left: 0px;">
                                                @switch($noticia->sentimento)
                                                    @case(0)
                                                        <span><i class="fa fa-smile-o fa-2x op-2"></i></span>                                                
                                                        <span><i class="fa fa-frown-o fa-2x op-2"></i></span>
                                                        <span><i class="fa fa-ban fa-2x text-info"></i></span>
                                                    @break
                                                    @case(-1)
                                                        <span><i class="fa fa-smile-o fa-2x op-2"></i></span>                                                
                                                        <span><i class="fa fa-frown-o fa-2x text-danger"></i></span>
                                                        <span><i class="fa fa-ban fa-2x op-2"></i></span>
                                                    @break
                                                    @case(1)
                                                        <span><i class="fa fa-smile-o fa-2x text-success"></i></span>                                                
                                                        <span><i class="fa fa-frown-o fa-2x op-2"></i></span>
                                                        <span><i class="fa fa-ban fa-2x op-2"></i></span>
                                                    @break  
                                                    @default
                                                        <span><i class="fa fa-smile-o fa-2x op-2"></i></span>                                                
                                                        <span><i class="fa fa-frown-o fa-2x op-2"></i></span>
                                                        <span><i class="fa fa-ban fa-2x op-2"></i></span>
                                                    @break                                           
                                                @endswitch
                                            </div>
                                        </div>     
                                    </div>                               
                                    <div style="position: absolute; bottom: 0px; right: 5px;">
                                        @if($noticia->cliente)
                                            <a title="Editar" href="{{ url('tv/noticias/'.$noticia->id.'/cliente/'.$noticia->cliente->id.'/editar') }}" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
                                            <a title="Excluir" href="{{ url('tv/noticias/'.$noticia->id.'/cliente/'.$noticia->cliente->id.'/remover') }}" class="btn btn-danger btn-link btn-icon btn-excluir"><i class="fa fa-trash fa-2x"></i></a>
                                        @else 
                                            <a title="Editar" href="{{ url('tv/noticias/'.$noticia->id.'/editar') }}" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
                                            <a title="Excluir" href="{{ url('tv/noticias/'.$noticia->id.'/remover') }}" class="btn btn-danger btn-link btn-icon btn-excluir"><i class="fa fa-trash fa-2x"></i></a>
                                        @endif
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
@endsection
@section('script')    
    <script>
      
        var host = $('meta[name="base-url"]').attr('content');

        $(document).ready(function(){ 

            var lista_ods = [["memória"]];

            var instance_ods = new Mark(context);
                              

                              var options = {
                                 "element": "mark",
                                 "separateWordSearch": false,
                                 "accuracy": {
                                    "value": "exactly",
                                    "limiters": [",", "."]
                                 },
                                 "diacritics": true
                              };

                              instance_ods.mark(lista_ods[0], options); 

        });

    </script>
@endsection