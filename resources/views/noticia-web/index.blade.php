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
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Coletas 
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
                <div class="col-lg-12 col-sm-12 mb-0 mt-0">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['noticia/web']]) !!}
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
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Buscar por <span class="text-primary">Digite o termo ou expressão de busca</span></label>
                                        <input type="text" class="form-control" name="termo" id="termo" minlength="3" placeholder="Termo" value="{{ $termo }}">
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
                                        <div class="col-lg-2 col-sm-12 img-{{ $dado->noticia_id }}" style="max-height: 300px; overflow: hidden;">   
                                            @if($dado->path_screenshot)                                         
                                                <img src="{{ Storage::disk('s3')->temporaryUrl($dado->path_screenshot, '+30 minutes') }}" 
                                                alt="Print notícia {{ $dado->noticia_id }}" 
                                                class="img-fluid img-thumbnail" 
                                                style="width: 100%; height: auto; border: none;">
                                            @else
                                                <img src="{{ asset('img/no-print.png') }}" 
                                                alt="Sem Print" 
                                                class="img-fluid img-thumbnail" 
                                                style="width: 100%; height: auto; border: none;">
                                            @endif
                                        </div>
                                        <div class="col-lg-10 col-sm-12"> 
                                            <div class="conteudo-{{ $dado->id }}">
                                                <p class="font-weight-bold mb-1">{{ $dado->titulo_noticia }}</p>
                                                @if($dado->fonte)
                                                    <h6><a href="{{ url('fonte-web/editar', $dado->fonte->id_fonte) }}" target="_BLANK">{{ ($dado->fonte) ? $dado->fonte->nome : '' }}</a></h6>  
                                                @endif
                                                <h6 style="color: #FF5722;">{{ ($dado->estado) ? $dado->estado->nm_estado : '' }}{{ ($dado->cidade) ? "/".$dado->cidae->nm_cidade : '' }}</h6> 
                                                <p class="text-muted mb-1"> {!! ($dado->data_noticia) ? date('d/m/Y', strtotime($dado->data_noticia)) : date('d/m/Y', strtotime($dado->data_noticia)) !!} - {{ ($dado->fonte) ? $dado->fonte->nome : '' }}</p> 
                                                <p class="mb-1">
                                                        <strong>Retorno de Mídia: </strong>{{ ($dado->nu_valor) ? "R$ ".$dado->nu_valor : 'Não calculado' }}
                                                    </p> 
                                                    <div>
                                                        @forelse($dado->clientes as $cliente)
                                                            <p class="mb-2">
                                                                <span>{{ $cliente->nome }}</span>
                                                                @switch($cliente->pivot->sentimento)
                                                                    @case(-1)
                                                                            <i class="fa fa-frown-o text-danger"></i>
                                                                            <a href="{{ url('noticia/'.$cliente->pivot->noticia_id.'/tipo/'.$cliente->pivot->tipo_id.'/cliente/'.$cliente->pivot->cliente_id.'/sentimento/0/atualizar') }}"><i class="fa fa-ban op-2"></i></a>
                                                                            <a href="{{ url('noticia/'.$cliente->pivot->noticia_id.'/tipo/'.$cliente->pivot->tipo_id.'/cliente/'.$cliente->pivot->cliente_id.'/sentimento/1/atualizar') }}"><i class="fa fa-smile-o op-2"></i></a>
                                                                        @break
                                                                    @case(0)
                                                                            <a href="{{ url('noticia/'.$cliente->pivot->noticia_id.'/tipo/'.$cliente->pivot->tipo_id.'/cliente/'.$cliente->pivot->cliente_id.'/sentimento/-1/atualizar') }}"><i class="fa fa-frown-o op-2"></i></a> 
                                                                            <i class="fa fa-ban text-primary"></i>
                                                                            <a href="{{ url('noticia/'.$cliente->pivot->noticia_id.'/tipo/'.$cliente->pivot->tipo_id.'/cliente/'.$cliente->pivot->cliente_id.'/sentimento/1/atualizar') }}"><i class="fa fa-smile-o op-2"></i></a>                                                
                                                                        @break
                                                                    @case(1)
                                                                            <a href="{{ url('noticia/'.$cliente->pivot->noticia_id.'/tipo/'.$cliente->pivot->tipo_id.'/cliente/'.$cliente->pivot->cliente_id.'/sentimento/-1/atualizar') }}"><i class="fa fa-frown-o op-2"></i></a>
                                                                            <a href="{{ url('noticia/'.$cliente->pivot->noticia_id.'/tipo/'.$cliente->pivot->tipo_id.'/cliente/'.$cliente->pivot->cliente_id.'/sentimento/0/atualizar') }}"><i class="fa fa-ban op-2"></i></a>
                                                                            <i class="fa fa-smile-o text-success"></i>
                                                                        @break                                            
                                                                @endswitch
                                                            </p>
                                                        @empty
                                                            <p class="text-danger mb-1">Nenhum cliente associada à notícia</p>
                                                        @endforelse
                                                    </div>
                                                    <div>
                                                        @forelse($dado->tags as $tag)
                                                            <span>#{{ $tag->nome }}</span>
                                                        @empty
                                                            <p class="text-danger mb-1">#Nenhuma tag associada à notícia</p>
                                                        @endforelse
                                                    </div>
                                            </div>
                                            <div class="sinopse-{{ $dado->id }}">
                                                {!! ($dado->conteudo->conteudo) ?  Str::limit($dado->conteudo->conteudo, 700, " ...")  : '<span class="text-danger">Nenhum conteúdo coletado</span>' !!}
                                            </div>
                                            
                                            <div>
                                                <button class="btn btn-primary btn-visualizar-noticia" data-id="{{ $dado->id }}"><i class="fa fa-eye"></i> Visualizar</button>
                                            </div>                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                <hr>
                                    <div class="stats">
                                        <i class="fa fa-refresh"></i>Notícia cadastrada em {{ \Carbon\Carbon::parse($dado->created_at)->format('d/m/Y H:i:s') }}
                                        <div class="pull-right">
                                            <a title="Excluir" href="{{ url('noticia/web/'.$dado->id.'/excluir') }}" class="btn btn-danger btn-fill btn-icon btn-sm btn-excluir" style="border-radius: 30px;">
                                                <i class="fa fa-times fa-3x text-white"></i>
                                            </a>
                                            <a title="Editar" href="{{ url('noticia/web/'.$dado->id.'/editar') }}" class="btn btn-primary btn-fill btn-icon btn-sm" style="border-radius: 30px;">
                                                <i class="fa fa-edit fa-3x text-white"></i>
                                            </a>
                                            <a title="Visualizar" href="{{ url('noticia/web/'.$dado->id.'/ver') }}" class="btn btn-warning btn-fill btn-icon btn-sm" style="border-radius: 30px;"><i class="fa fa-link fa-3x text-white"></i></a>
                                            <a title="Visualizar" href="{{ $dado->url_noticia }}" target="_BLANK" class="btn btn-success btn-fill btn-icon btn-sm" style="border-radius: 30px;"><i class="fa fa-globe fa-3x text-white"></i></a>
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
<div class="modal fade" id="showNoticia" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-scrollable modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header" style="padding: 15px !important;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h6 style="text-align: left;" class="modal-title" id="exampleModalLabel"><i class="fa fa-newspaper-o"></i><span></span> Dodos da Notícia</h6>
        </div>
        <div class="modal-body" style="padding: 15px;">
            <div class="row">
                <div class="col-md-12 modal-conteudo"></div>
                <div class="col-md-12 modal-sinopse"></div>
            </div>
            <div class="center">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>
            </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {

            var host =  $('meta[name="base-url"]').attr('content');
            var token = $('meta[name="csrf-token"]').attr('content');

            $(".btn-visualizar-noticia").click(function(){

                var id = $(this).data("id");
                var chave = ".conteudo-"+id;
                var sinopse = ".sinopse-"+id;

                $(".modal-conteudo").html($(chave).html());
              
                $(".modal-sinopse").html($(sinopse).text().replace(/\n/g, "<br />"));

                $("#showNoticia").modal("show");

            });

        });
    </script>
@endsection