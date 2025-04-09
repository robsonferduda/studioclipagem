@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title ml-3">
                        <i class="fa fa-newspaper-o"></i> Impressos 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Notícias 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Extrair
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('impresso') }}" class="btn btn-warning pull-right mr-3"><i class="nc-icon nc-chart-pie-36"></i> Dashboard</a>
                    <a href="{{ url('noticias/impresso') }}" class="btn btn-info pull-right mr-3"><i class="fa fa-newspaper-o"></i> Listar Notícias</a>
                </div>
            </div>
        </div>
        <div class="card-body mr-3 ml-2">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    {!! Form::open(['id' => 'frm_social_search', 'class' => 'form-horizontal', 'url' => ['noticia-impressa', $noticia->id], 'method' => 'patch']) !!}

                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data de Cadastro</label>
                                        <input type="text" class="form-control datepicker" name="dt_cadastro" required="true" value="{{ ($noticia->dt_cadastro) ? \Carbon\Carbon::parse($noticia->dt_cadastro)->format('d/m/Y') : '' }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data do Clipping</label>
                                        <input type="text" class="form-control datepicker" name="dt_clipagem" required="true" value="{{ ($noticia->dt_clipagem) ? \Carbon\Carbon::parse($noticia->dt_clipagem)->format('d/m/Y') : '' }}" placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Fonte</label>
                                            <select class="form-control select2" name="id_fonte" id="id_fonte" required="true">
                                                <option value="">Selecione uma fonte</option>
                                                @foreach ($fontes as $fonte)
                                                    <option {{ ($fonte->id == $noticia->id_fonte) ? 'selected' : '' }} value="{{ $fonte->id }}">{{ $fonte->nome }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Seção</label>
                                        <input type="hidden" name="valor_id_sessao_impresso" id="valor_id_sessao_impresso" value="{{ $noticia->id_sessao_impresso }}">
                                        <select class="form-control select2" name="id_sessao_impresso" id="id_sessao_impresso" disabled="true">
                                            <option value="">Selecione uma seção</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Título</label>
                                        <input type="text" class="form-control" name="titulo" id="titulo" placeholder="Título" value="{{ ($noticia->titulo) ? $noticia->titulo : '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Estado </label>
                                        <select class="form-control selector-select2" name="cd_estado" id="cd_estado">
                                            <option value="">Selecione um estado</option>
                                            @foreach ($estados as $estado)
                                                <option value="{{ $estado->cd_estado }}" {!! $noticia->cd_estado == $estado->cd_estado ? " selected" : '' !!}>
                                                    {{ $estado->nm_estado }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Cidade </label>
                                        <input type="hidden" name="cd_cidade" id="cd_cidade" value="{{ ($noticia->cd_cidade) ? $noticia->cd_cidade : 0  }}">
                                        <select class="form-control select2" name="cidade" id="cidade" disabled="disabled">
                                            <option value="">Selecione uma cidade</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Página Atual</label>
                                        <input type="text" class="form-control" name="nu_pagina_atual" id="nu_pagina_atual" placeholder="Número" value="{{ ($noticia->nu_pagina_atual) ? $noticia->nu_pagina_atual : '' }}">
                                    </div>                                    
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Total de Páginas</label>
                                        <input type="text" class="form-control" name="nu_paginas_total" id="nu_paginas_total" placeholder="Número" value="{{ ($noticia->nu_paginas_total) ? $noticia->nu_paginas_total : '' }}">
                                    </div>                                    
                                </div>
                                <div class="col-md-8 col-sm-12">
                                    <div class="form-group">
                                        <label>Link</label>
                                        <input type="text" class="form-control" name="ds_link" id="ds_link" placeholder="URL" value="{{ $noticia->ds_link }}">
                                    </div>                                    
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label>Colunas</label>
                                        <input type="text" class="form-control monetario" name="nu_colunas" id="nu_colunas" placeholder="Colunas" value="{{ ($noticia->nu_colunas) ? $noticia->nu_colunas : '' }}">
                                    </div>                                    
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label>Altura</label>
                                        <input type="text" class="form-control monetario" name="nu_altura" id="nu_altura" placeholder="Altura" value="{{ ($noticia->nu_altura) ? $noticia->nu_altura : '' }}">
                                    </div>                                    
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label>Largura</label>
                                        <input type="text" class="form-control monetario" name="nu_largura" id="nu_largura" placeholder="Largura" value="{{ $noticia->nu_largura }}">
                                    </div>                                    
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label>Retorno</label>
                                        <input type="text" class="form-control monetario" name="valor_retorno" id="valor_retorno" placeholder="Retorno" value="{{ $noticia->valor_retorno }}" readonly>
                                    </div>                                    
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="sinopse">Sinopse</label>
                                    <div class="form-group">
                                        <textarea class="form-control" name="sinopse" id="sinopse" rows="10">{!! $noticia->texto !!}</textarea>
                                    </div>
                                </div>                            
                            </div>     
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Print da Notícia</label>
                                        <div class="row">
                                            @if($noticia->fl_copia and $noticia->print)
                                                <div class="col-md-12">
                                                    <img src="{{ asset('jornal-impresso/noticias/'.$noticia->print) }}" alt="Recorte do Jornal">
                                                </div>
                                            @else
                                                <div class="col-md-9">
                                                    <div class="img-container">
                                                        <img id="image" src="{{ asset('img/noticia-impressa/'.$noticia->ds_caminho_img) }}" alt="Recorte do Jornal">
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
                                                            <span class="input-group-text input-impresso" id="basic-addon1">Largura</span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nu_largura" id="dataWidth" placeholder="0">
                                                        </div>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                            <span class="input-group-text input-impresso" id="basic-addon1">Altura</span>
                                                            </div>
                                                            <input type="text" class="form-control" name="nu_altura" id="dataHeight" placeholder="0">
                                                        </div>                                                                            
                                                    </div>
                                                    <div class="docs-buttons center">
                                                        <button type="button" class="btn btn-info" data-method="getCroppedCanvas" data-option="">
                                                            <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="">
                                                                <i class="fa fa-crop"></i> Recortar e Atualizar
                                                            </span>
                                                        </button>
                                                    </div>
                                                </div>                                                
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center mb-2 mt-3">
                                <button type="submit" class="btn btn-success" name="btn_enviar" value="salvar"><i class="fa fa-save"></i> Salvar</button>
                                <button type="submit" class="btn btn-warning" name="btn_enviar" value="salvar_e_copiar"><i class="fa fa-copy"></i> Salvar e Copiar</button>
                                <a href="{{ url('noticias/impresso') }}" class="btn btn-danger"><i class="fa fa-times"></i> Cancelar</a>
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
                  <h5 class="modal-title" id="getCroppedCanvasTitle">Imagem Recortada

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                  </h5>
                 
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer center">
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