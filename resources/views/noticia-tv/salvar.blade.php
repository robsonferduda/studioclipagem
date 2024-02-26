@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-tv ml-3"></i> TV 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Decupagem 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Salvar 
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('tv/noticias/cadastrar') }}" class="btn btn-primary pull-right" style="margin-right: 12px;"><i class="fa fa-plus"></i> Cadastrar Notícia</a>
                    <a href="{{ url('tv/noticias') }}" class="btn btn-info pull-right mr-3"><i class="fa fa-table"></i> Notícias</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row mr-1 ml-1">
            <div class="col-md-12">
                <h6>Dados da Notícia</h6>
            </div>
            <div class="col-lg-12 col-md-3 mb-12">                
                <div class="form-group">                    
                    {{ Form::open(array('url' => 'noticia_tv/decupagem/salvar', 'method' => 'POST', 'name'=>'product_images')) }}
                        @foreach($textos as $key => $text) 
                            <div class="box_{{ $key }}">
                                <label for="sinopse">Sinopse <span class="add-complemento text-primary" data-id="{{ $key }}">Vincular Cliente</span> <span class="excluir-sinopse text-danger" data-id="{{ $key }}">Excluir</span></label>   
                                <textarea class="form-control mb-2" name="sinopse[]" id="sinopse_{{ $key }}">{!! nl2br($text) !!}</textarea> 
                            </div>                     
                        @endforeach
                        <div class="col-lg-12 col-md-3 mb-12 text-center">
                            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Salvar</button>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>  
        </div>
    </div>
</div> 
<div class="modal fade" id="modalCliente" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h6 style="text-align: left;" class="modal-title" id="exampleModalLabel"><i class="nc-icon nc-briefcase-24"></i> Vincular Cliente</h6>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Cliente <span class="text-danger">Obrigatório</span></label>
                        <input hidden name="cliente_id" id="cliente_id" value="">
                        <select class="form-control cliente select2" name="cliente" id="cliente">
                            <option value="">Selecione um cliente</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Área do Cliente <span class="text-info">Opcional</span></label>
                        <select class="form-control select2" name="area" id="area" disabled>
                            <option value="">Selecione uma área</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Sentimento <span class="text-info">Opcional</span></label>
                        <select class="form-control" name="sentimento" id="sentimento">
                            <option value="">Selecione um sentimento</option>
                            <option value="1">Positivo</option>
                            <option value="0">Neutro</option>
                            <option value="-1">Negativo</option>
                        </select>
                    </div>
                </div>
             
                <div class="col-md-12 center">
                    <div class="form-group mt-3">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>
                        <button type="button" class="btn btn-success btn-add-cliente"><i class="fa fa-plus"></i>Adicionar</button>
                    </div>
                </div>
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

            $(document).on("click", ".add-complemento", function() {
                var id = $(this).data("id");

                $('#modalCliente').modal('show');
                
            });

            $(document).on("click", ".excluir-sinopse", function() {
                var id = $(this).data("id");

                texto = '#sinopse_'+id;
                $(texto).remove();

                box = '.box_'+id;
                $(box).remove();
                
            });

        });
    </script>
@endsection