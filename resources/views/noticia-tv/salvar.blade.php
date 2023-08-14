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
            <div class="col-md-12">
                <h6>Dados da Notícia</h6>
            </div>
            <input type="hidden" name="clientes[]" id="clientes">
            <div class="col-md-2">
                <div class="form-group">
                    <label>Data <span class="text-danger">Obrigatório</span></label>
                    <input type="text" class="form-control datepicker" name="data" id="data" placeholder="__/__/____" required value="{!! !empty($dados->dt_noticia) ? date('d/m/Y', strtotime($dados->dt_noticia)) : '' !!}">
                </div>
            </div>
            <div class="col-lg-12 col-md-3 mb-12">                
                <div class="form-group">                    
                    {{ Form::open(array('url' => 'noticia_tv/decupagem/salvar', 'method' => 'POST', 'name'=>'product_images')) }}
                        <p class="text-info">Cada caixa de texto irá gerar uma nova notícia, repetindo os demais dados do formulário.</p>
                        @foreach($textos as $key => $text) 
                            <div class="box_{{ $key }}">
                                <label for="sinopse">Sinopse <span class="excluir-sinopse text-danger" data-id="{{ $key }}">Excluir</span></label>   
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
@endsection
@section('script')
    <script>

        $(document).ready(function() { 

            var host =  $('meta[name="base-url"]').attr('content');

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