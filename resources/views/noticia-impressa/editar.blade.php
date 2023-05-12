@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title ml-3">
                        <i class="fa fa-newspaper-o"></i> Jornal Impresso 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Notícias 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Editar
                    </h4>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary pull-right mr-3"><i class="fa fa-plus"></i> Novo</button>
                    <a href="{{ url('monitoramento/executar') }}" class="btn btn-warning pull-right mr-3"><i class="fa fa-bolt"></i> Executar</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['noticia-impressa', $noticia->id], 'method' => 'patch']) !!}
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Cliente</label>
                                        <select class="form-control select2" name="cliente" id="cliente">
                                            <option value="">Selecione um cliente</option>
                                            @foreach ($clientes as $cliente)
                                                <option value="{{ $cliente->id }}" {{ ($cliente->id == $vinculo->cliente_id) ? 'selected' : '' }}>{{ $cliente->pessoa->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Título</label>
                                        <input type="text" class="form-control" name="titulo" value="{{ $noticia->titulo }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Texto</label>
                                        <textarea class="form-control texto-noticia-field" id="texto" name="texto" rows="15">{!! $noticia->texto !!}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Imagem</label>
                                        <div class="row">
                                            <div class="col-md-9">
                                              <div class="img-container">
                                                <img id="image" src="{{ asset('jornal-impresso/'.$noticia->fonte->codigo.'/'.\Carbon\Carbon::parse($noticia->dt_clipagem)->format('Ymd').'/img/pagina_'.$noticia->nu_pagina_atual.'.png') }}" alt="Recorte do Jornal">
                                              </div>
                                            </div>
                                            <div class="col-md-3">
                                                <!-- <h3>Preview:</h3> -->
                                                <div class="docs-preview clearfix">
                                                  <div class="img-preview preview-lg"></div>
                                                  <div class="img-preview preview-md"></div>
                                                  <div class="img-preview preview-sm"></div>
                                                  <div class="img-preview preview-xs"></div>
                                                </div>
                                        
                                                <!-- <h3>Data:</h3> -->
                                                <div class="docs-data">
                                                                                                       
                                                    <div class="input-group mb-3">
                                                      <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1">Largura</span>
                                                      </div>
                                                      <input type="text" class="form-control" id="dataWidth" placeholder="x">
                                                    </div>

                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                          <span class="input-group-text" id="basic-addon1">Altura</span>
                                                        </div>
                                                        <input type="text" class="form-control" id="dataHeight" placeholder="x">
                                                      </div>
                                                  
                                                                                           
                                                  
                                                </div>

                                                <div class="docs-buttons center">
                                                    <button type="button" class="btn btn-info" data-method="getCroppedCanvas" data-option="{ &quot;maxWidth&quot;: 4096, &quot;maxHeight&quot;: 4096 }">
                                                        <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="$().cropper(&quot;getCroppedCanvas&quot;, { maxWidth: 4096, maxHeight: 4096 })">
                                                            <i class="fa fa-eye"></i> Ver Recorte
                                                        </span>
                                                      </button>
                                                </div>
                                              </div>
                                        </div>
                                    </div>
                                </div>                                
                                <div class="col-md-12 checkbox-radios mb-0">
                                    <button type="submit" id="btn-find" class="btn btn-success mb-3"><i class="fa fa-save"></i> Atualizar</button>
                                </div>
                            </div>     
                        </div>
                    {!! Form::close() !!}
                </div>               
            </div>
        </div>
    </div>
</div> 

        <!-- Show the cropped image in modal -->
        <div class="modal fade docs-cropped" id="getCroppedCanvasModal" aria-hidden="true" aria-labelledby="getCroppedCanvasTitle" role="dialog" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="getCroppedCanvasTitle">Imagem Recortada</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                  <a class="btn btn-primary" id="download" href="javascript:void(0);" download="cropped.jpg">Atualizar Imagem</a>
                </div>
              </div>
            </div>
          </div><!-- /.modal -->
@endsection
@section('script')
<script>
    $( document ).ready(function() {

      

    });
</script>
@endsection