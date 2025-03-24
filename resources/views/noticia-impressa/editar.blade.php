@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title ml-3">
                        <i class="fa fa-newspaper-o"></i> Impressos 
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Cadastrar Notícia
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('impresso') }}" class="btn btn-warning pull-right mr-3"><i class="nc-icon nc-chart-pie-36"></i> Dashboard</a>
                    <a href="{{ url('noticias/impresso') }}" class="btn btn-info pull-right mr-3"><i class="fa fa-newspaper-o"></i> Listar Notícias</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            <div class="row"> 
                <div class="col-lg-12 col-sm-12">
                    {!! Form::open(['id' => 'frm_user_edit', 'url' => ['noticia-impressa', $noticia->id], 'method' => 'patch']) !!}
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
                                <div class="col-md-3">
                                    <label for="arquivo">Print da Notícia</label>
                                    <div style="min-height: 302px;" class="dropzone" id="dropzone"><div class="dz-message" data-dz-message><span>CLIQUE AQUI<br/> ou <br/>ARRASTE</span></div></div>
                                    <input type="hidden" name="arquivo" id="arquivo">
                                </div>
                                <div class="col-md-9">
                                    <label for="sinopse">Sinopse</label>
                                    <div class="form-group">
                                        <textarea class="form-control" name="sinopse" id="sinopse" rows="10">{{ $noticia->sinopse }}</textarea>
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
@endsection
@section('script')
<script>
    $( document ).ready(function() {

        var host = $('meta[name="base-url"]').attr('content');
        var id_fonte = $('#id_fonte').val();

        if(id_fonte != ''){
            buscarSecoes(id_fonte);
        }

        $(document).on('change', '.monetario', function() {
                
            var retorno = 0;
            var altura = ($("#nu_altura").val()) ? $("#nu_altura").val() : 1;
            var largura = ($("#nu_largura").val()) ? $("#nu_largura").val() : 1;
            var colunas = ($("#nu_colunas").val()) ? $("#nu_colunas").val() : 1;

            retorno = altura * largura * colunas;

            $("#valor_retorno").val(retorno);
        });

        $(document).on('change', '#id_fonte', function() {
                
            var fonte = $(this).val();
            buscarSecoes(fonte);
            return $('#id_sessao_impresso').prop('disabled', false);
        });


        function buscarSecoes(id_fonte){

            var valor_id_sessao_impresso = $("#valor_id_sessao_impresso").val();

            $.ajax({
                    url: host+'/noticia/impresso/fonte/sessoes/'+id_fonte,
                    type: 'GET',
                    beforeSend: function() {
                        $('.content').loader('show');
                        $('#id_sessao_impresso').append('<option value="">Buscando seções...</option>').val('');
                    },
                    success: function(data) {

                        $('#id_sessao_impresso').find('option').remove();
                        $('#id_sessao_impresso').attr('disabled', false);

                        if(data.length == 0) {                            
                            $('#id_sessao_impresso').append('<option value="">Fonte não possui seções cadastradas</option>').val('');
                            return;
                        }

                        $('#id_sessao_impresso').append('<option value="">Selecione uma seção</option>').val('');

                        data.forEach(element => {
                            let option = new Option(element.ds_sessao, element.id_sessao_impresso);
                            $('#id_sessao_impresso').append(option);
                        });
                        
                    },
                    complete: function(){
                    
                        if(valor_id_sessao_impresso > 0)
                            $('#id_sessao_impresso').val(valor_id_sessao_impresso);
                    
                        $('.content').loader('hide');
                    }
                });

        };

    });

    $( document ).ready(function() {

        var valor_id_sessao_impresso = $("#valor_id_sessao_impresso").val();

        if(valor_id_sessao_impresso > 0)
            $('#id_sessao_impresso').val(valor_id_sessao_impresso);
    });
</script>
@endsection