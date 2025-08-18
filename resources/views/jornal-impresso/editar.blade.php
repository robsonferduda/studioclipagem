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
                    {!! Form::open(['id' => 'frm_impresso', 'class' => 'form-horizontal', 'url' => ['noticia-impressa', $noticia->id], 'method' => 'patch']) !!}
                        <input type="hidden" name="noticia_id" id="noticia_id" value="{{ $noticia->id }}">
                        <input type="hidden" name="id_noticia" id="id_noticia" value="{{ $noticia->id }}">
                        <div class="form-group m-3 w-70">
                            <div class="row">
                                <input type="hidden" name="clientes[]" id="clientes">
                                <input type="hidden" name="ds_caminho_img" id="ds_caminho_img">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>Cliente</label>
                                        <select class="form-control cliente select2" name="cd_cliente" id="cd_cliente">
                                            <option value="">Selecione um cliente</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Área do Cliente <span class="text-info add-area" data-toggle="modal" data-target="#modalArea">Adicionar Área</span></label>
                                        <select class="form-control area select2" name="cd_area" id="cd_area" disabled>
                                            <option value="">Selecione uma área</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Sentimento </label>
                                        <select class="form-control" name="cd_sentimento" id="cd_sentimento">
                                            <option value="">Selecione um sentimento</option>
                                            <option value="1">Positivo</option>
                                            <option value="0">Neutro</option>
                                            <option value="-1">Negativo</option>
                                        </select>
                                    </div>                        
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-success btn-add-cliente mt-4 w-100"><i class="fa fa-plus"></i></button>
                                </div>
                                
                                <div class="col-md-12">
                                    <ul class="list-unstyled metadados"></ul>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data de Cadastro</label>
                                        <input type="text" 
                                        class="form-control datepicker" 
                                        name="dt_cadastro" 
                                        readonly 
                                        required="true" 
                                        value="{{ date("d/m/Y") }}" 
                                        placeholder="__/__/____">
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Data do Clipping</label>
                                        <input type="text" 
                                        class="form-control datepicker" 
                                        name="dt_clipagem" 
                                        required="true" 
                                        value="{{ ($noticia->dt_clipagem) ? \Carbon\Carbon::parse($noticia->dt_clipagem)->format('d/m/Y') : '' }}" 
                                        placeholder="__/__/____">
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
                                        <label>Seção <span class="text-primary add-secao" data-toggle="modal" data-target="#addSecao">Adicionar Seção</span></label>
                                        <select class="form-control select2" name="id_sessao_impresso" id="id_sessao_impresso">
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
                                <div class="col-md-2">
                                    <input type="hidden" name="nu_valor_fonte" id="nu_valor_fonte">
                                    <div class="form-group">
                                        <label>Localização <span class="valor_cm text-info"></span></label>
                                        <select class="form-control" name="local_impressao" id="local_impressao">
                                            <option value="">Selecione um local</option>
                                            <option value="valor_cm_capa_semana" {{ ($noticia->local_impressao == 'valor_cm_capa_semana') ? 'selected' : '' }}>Capa</option>
                                            <option value="valor_cm_capa_fim_semana" {{ ($noticia->local_impressao == 'valor_cm_capa_fim_semana') ? 'selected' : '' }}>Capa FDS</option>
                                            <option value="valor_cm_contracapa" {{ ($noticia->local_impressao == 'valor_cm_contracapa') ? 'selected' : '' }}>Contracapa</option>
                                            <option value="valor_cm_demais_semana" {{ ($noticia->local_impressao == 'valor_cm_demais_semana') ? 'selected' : '' }}>Demais Páginas</option>
                                            <option value="valor_cm_demais_fim_semana" {{ ($noticia->local_impressao == 'valor_cm_demais_fim_semana') ? 'selected' : '' }}>Demais Páginas FDS</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Colunas</label>
                                        <input type="text" class="form-control monetario" name="nu_colunas" id="nu_colunas" placeholder="Colunas" value="{{ ($noticia->nu_colunas) ? $noticia->nu_colunas : 0 }}">
                                    </div>                                    
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Largura <span class="text-info">em cm</span></label>
                                        <input type="text" class="form-control monetario" name="nu_largura" id="nu_largura" placeholder="Largura" value="{{ ($noticia->nu_largura) ? $noticia->nu_largura : 0 }}">
                                    </div>                                    
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Altura <span class="text-info">em cm</span></label>
                                        <input type="text" class="form-control monetario" name="nu_altura" id="nu_altura" placeholder="Altura" value="{{ ($noticia->nu_altura) ? $noticia->nu_altura : 0 }}">
                                    </div>                                    
                                </div>                                
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label>Retorno</label>
                                        <input type="text" class="form-control monetario" name="valor_retorno" id="valor_retorno" placeholder="Retorno" value="{{ ($noticia->valor_retorno) ? $noticia->valor_retorno : 0 }}">
                                    </div>                                    
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">    
                                        <label for="tags[]">TAGs</label>
                                        <select name="tags[]" multiple="multiple" class="form-control select2">
                                            @foreach ($tags as $tag)
                                                <option value="{{ $tag->id }}" {{ ($noticia->tags->contains($tag->id)) ? 'selected'  : '' }}>{{ $tag->nome }}</option>
                                            @endforeach
                                        </select> 
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
                                    <h6><i class="fa fa-picture-o"></i> Opções de Imagem</h6>
                                </div>
                                <div class="col-md-3">  
                                    <h6 class="mt-3 mb-0"><i class="fa fa-picture-o"></i> Página Original</h6>                                  
                                    @if($pagina)                                        
                                        <a href="{{ url('jornal-impresso/web/pagina/download/'.$pagina->id) }}" target="_BLANK"><img src="{{ Storage::disk('s3')->temporaryUrl($pagina->path_pagina_s3, '+2 minutes') }}"></a>
                                        <p><a href="{{ url('jornal-impresso/web/pagina/download/'.$pagina->id) }}" target="_BLANK"><span class="text-info">Clique para baixar</span></a></p>
                                    @endif
                                </div>
                                <div class="col-md-3">
                                    <h6 class="mt-3"><i class="fa fa-scissors" aria-hidden="true"></i> Página Recortada</h6>
                                    <img src="{{ asset('img/noticia-impressa/'.$noticia->ds_caminho_img) }}" alt="Página {{ $noticia->n_pagina }}">
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mt-3"><i class="fa fa-upload" aria-hidden="true"></i> Upload - Print da Notícia</h6>
                                    <div style="min-height: 302px;" class="dropzone" id="dropzone"><div class="dz-message" data-dz-message><span>CLIQUE AQUI<br/> ou <br/>ARRASTE</span></div></div>
                                    <input type="hidden" name="arquivo" id="arquivo">
                                </div>
                            </div>  
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <h6 class="mt-3"><i class="fa fa-scissors" aria-hidden="true"></i> REcortar Página</h6>
                                        <div class="row">
                                           
                                                <div class="col-md-12">
                                                    <div class="img-container">
                                                        <img id="image" src="{{ asset('img/noticia-impressa/'.$noticia->ds_caminho_img) }}" alt="Recorte do Jornal">
                                                    </div>
                                                </div>

                                                <div class="col-md-3">

                                                    @if($pagina)
                                                        <h6 class="mt-3">Imagem Original 
                                                            <a href="{{ url('jornal-impresso/web/pagina/download/'.$pagina->id) }}" target="_BLANK"><span class="text-info">Clique para baixar</span></a>
                                                        </h6>
                                                        <a href="{{ url('jornal-impresso/web/pagina/download/'.$pagina->id) }}" target="_BLANK"><img src="{{ Storage::disk('s3')->temporaryUrl($pagina->path_pagina_s3, '+2 minutes') }}"></a>
                                                    @endif

                                                    <!-- <h3>Preview:</h3> -->
                                                    <div class="docs-preview clearfix">
                                                    <div class="img-preview preview-lg"></div>
                                                    <div class="img-preview preview-md"></div>
                                                    <div class="img-preview preview-sm"></div>
                                                    <div class="img-preview preview-xs"></div>
                                                    </div>
                                            
                                                    <!-- <h3>Data:</h3> -->
                                                    <div class="docs-data mt-2">                                                                                                       
                                                        <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text input-impresso" id="basic-addon1">Largura</span>
                                                        </div>
                                                        <input type="text" class="form-control" name="nu_largura_px" id="dataWidth" placeholder="0">
                                                        </div>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                            <span class="input-group-text input-impresso" id="basic-addon1">Altura</span>
                                                            </div>
                                                            <input type="text" class="form-control" name="nu_altura_px" id="dataHeight" placeholder="0">
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
                                          
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center mb-2 mt-3">
                                <button type="submit" class="btn btn-success" name="btn_enviar" id="btn_enviar" value="salvar"><i class="fa fa-save"></i> Salvar</button>
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
<div class="modal fade" id="addSecao" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h6 style="text-align: left;" class="modal-title" id="exampleModalLabel"><i class="fa fa-bookmark "></i> Adicionar Seção</h6>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Nome da Seção</label>
                            <input type="mail" class="form-control" name="ds_sessao" id="ds_sessao">
                        </div>
                    </div>
                </div>
                <div class="center">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>
                    <button type="button" class="btn btn-success btn-salvar-secao"><i class="fa fa-save"></i> Salvar</button>
                </div>
        </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalArea" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h6 style="text-align: left;" class="modal-title" id="exampleModalLabel"><i class="fa fa-tags"></i> Adicionar Área</h6>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Área</label>
                        <input type="text" class="form-control" name="ds_area" id="ds_area" placeholder="Descrição">
                    </div>
                </div>             
            <div class="col-md-12 center">
                <div class="form-group mt-3">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>
                    <button type="button" class="btn btn-success btn-add-area"><i class="fa fa-plus"></i> Adicionar</button>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="modalFonte" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h6 style="text-align: left;" class="modal-title" id="exampleModalLabel"><i class="fa fa-database"></i> Adicionar Fonte</h6>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Fonte</label>
                            <input type="text" class="form-control" name="nome" id="nome" placeholder="Descrição">
                        </div>
                    </div>             
                    <div class="col-md-12 center">
                        <div class="form-group mt-3">
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>
                            <button type="button" class="btn btn-success btn-add-fonte"><i class="fa fa-plus"></i> Adicionar</button>
                        </div>
                    </div>
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
<script src="{{ asset('js/formulario-cadastro.js') }}"></script>
<script>
    $( document ).ready(function() {



    });
</script>
@endsection