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
                                        <label>Fonte <span class="text-danger"></label>
                                        <input type="hidden" name="fonte" id="id_fonte" value="">
                                        <div class="input-group">
                                            <input type="text" class="form-control" style="height: 40px;" id="nome_fonte" placeholder="Selecione uma fonte" value="" readonly>
                                            <div class="input-group-append">
                                                <button type="button" style="margin: 0px;" class="btn btn-primary" data-toggle="modal" data-target="#modalFonte">Buscar Fonte</button>
                                            </div>
                                        </div>
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
                                    <a href="{{ url('noticia/web') }}" class="btn btn-warning btn-limpar mb-3"><i class="fa fa-refresh"></i> Limpar</a>
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
                                                            'cliente' => $cliente_selecionado,
                                                            'tipo_data' =>$tipo_data,
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
                                        <div class="col-lg-2 col-sm-12 img-{{ $dado->id }}" style="max-height: 300px; overflow: hidden;">   
                                            @if($dado->ds_caminho_img)
                                                <img src="{{ asset('img/noticia-web/'.$dado->ds_caminho_img) }}" alt="Página {{ $dado->ds_caminho_img }}">
                                            @elseif($dado->path_screenshot)                                         
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
                                                <div class="clientes-noticia clientes-noticia-{{ $dado->id }}" data-id="{{ $dado->id }}" data-tipo="2">
                                                        
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
                                                {!! ($dado->conteudo) ? Str::limit($dado->conteudo->conteudo, 700, " ...") : 'Notícia sem conteúdo' !!}
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
                                        <i class="fa fa-refresh"></i>Cadastrado por <strong>{{ ($dado->usuario) ? $dado->usuario->name : 'Sistema' }}</strong> em {{ \Carbon\Carbon::parse($dado->created_at)->format('d/m/Y H:i:s') }}. Última atualização em {{ \Carbon\Carbon::parse($dado->updated_at)->format('d/m/Y H:i:s') }}
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
                                                        'cliente' => $cliente_selecionado,
                                                        'tipo_data' =>$tipo_data,
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
                <div class="col-md-12 modal-img center"></div>
            </div>
            <div class="center">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>
            </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalFonte" tabindex="-1" role="dialog" aria-labelledby="modalFonteLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-scrollable modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header" style="padding: 15px !important;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h6 style="text-align: left;" class="modal-title" id="exampleModalLabel"><i class="fa fa-newspaper-o"></i><span></span> SELEÇÃO DE FONTES</h6>
        </div>
        <div class="modal-body" style="padding: 15px;">
            <form id="formBuscaFonte" class="form-inline mb-3">
                <div class="row">
                    <div class="col-md-12">
                        <input type="text" class="form-control mr-2 mb-2" id="filtro_nome" placeholder="Nome da Fonte" style="width: 35%;">
                        <select class="form-control mr-2 mb-2" name="cd_estado" id="filtro_estado" style="width: 30%;">
                            <option value="">Selecione um estado</option>
                            @foreach ($estados as $estado)
                                <option value="{{ $estado->cd_estado }}">
                                    {{ $estado->nm_estado }}
                                </option>
                            @endforeach
                        </select>
                        <select class="form-control mr-2 mb-2" name="cd_cidade" id="filtro_cidade" style="width: 30%;" disabled="disabled">
                            <option value="">Selecione uma cidade</option>
                        </select>
                    </div>
                    <div class="col-md-12 center">
                        <button type="button" class="btn btn-info mb-2" id="btnBuscarFonte"><i class="fa fa-search"></i> Buscar</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>
                    </div>
                </div>
            </form>
        <div id="resultadoFontes">
          <!-- Resultados AJAX aqui -->
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
@section('script')
<script src="{{ asset('js/noticia_clientes.js') }}"></script>
    <script>
        $(document).ready(function() {

            var host =  $('meta[name="base-url"]').attr('content');
            var token = $('meta[name="csrf-token"]').attr('content');

            $(".btn-visualizar-noticia").click(function(){

                var id = $(this).data("id");
                var chave = ".conteudo-"+id;
                var sinopse = ".sinopse-"+id;
                var img = ".img-"+id;

                $(".modal-conteudo").html($(chave).html());              
                $(".modal-sinopse").html($(sinopse).text().replace(/\n/g, "<br />"));
                $(".modal-img").html($(img).html());

                $("#showNoticia").modal("show");

            });

             function buscarFontes(pagina = 1) {
            var nome = $('#filtro_nome').val();
            var estado = $('#filtro_estado').val();
            var cidade = $('#filtro_cidade').val();

            // Limpa o resultado antes de buscar
            $('#resultadoFontes').html('');

            $.ajax({
                url: '{{ url("fonte-web/buscar/combo") }}',
                type: 'GET',
                data: {
                    nome: nome,
                    estado: estado,
                    cidade: cidade,
                    page: pagina
                },
                success: function(res) {
                    var html = '<table class="table table-bordered"><tr><th>Nome</th><th>Estado</th><th>Cidade</th><th>Ação</th></tr>';
                    if(res.data.length == 0) {
                        html += '<tr><td colspan="4">Nenhuma fonte encontrada.</td></tr>';
                    } else {
                        $.each(res.data, function(i, fonte) {
                            html += '<tr>';
                            html += '<td>' + fonte.nome + '</td>';
                            html += '<td>' + (fonte.estado || '') + '</td>';
                            html += '<td>' + (fonte.cidade || '') + '</td>';
                            html += '<td><button type="button" class="btn btn-success btn-sm selecionar-fonte" data-id="'+fonte.id+'" data-nome="'+fonte.nome+'">Selecionar</button></td>';
                            html += '</tr>';
                        });
                    }
                    html += '</table>';

                     // Paginação customizada
                    var current = res.current_page;
                    var last = res.last_page;
                    var start = Math.max(1, current - 5);
                    var end = Math.min(last, start + 9);
                    start = Math.max(1, end - 9); // Garante que sempre mostre até 10 páginas

                    html += '<nav><ul class="pagination justify-content-center">';

                    // Botão anterior
                    if(current > 1) {
                        html += '<li class="page-item"><a class="page-link paginacao-fonte" href="#" data-pagina="'+(current-1)+'">&laquo; Anterior</a></li>';
                    } else {
                        html += '<li class="page-item disabled"><span class="page-link">&laquo; Anterior</span></li>';
                    }

                    // Números das páginas
                    for(var i = start; i <= end; i++) {
                        html += '<li class="page-item '+(i==current?'active':'')+'"><a class="page-link paginacao-fonte" href="#" data-pagina="'+i+'">'+i+'</a></li>';
                    }

                    // Botão próxima
                    if(current < last) {
                        html += '<li class="page-item"><a class="page-link paginacao-fonte" href="#" data-pagina="'+(current+1)+'">Próxima &raquo;</a></li>';
                    } else {
                        html += '<li class="page-item disabled"><span class="page-link">Próxima &raquo;</span></li>';
                    }

                    html += '</ul></nav>';

                    $('#resultadoFontes').html(html);
                }
            });
        }

            // Evento de clique no botão de buscar fontes
            $('#btnBuscarFonte').click(function() {
                buscarFontes();
            });

            // Evento de clique na paginação
            $(document).on('click', '.paginacao-fonte', function(e) {
                e.preventDefault();
                var pagina = $(this).data('pagina');
                buscarFontes(pagina);
            });

            // Evento de seleção da fonte
            $(document).on('click', '.selecionar-fonte', function() {
                var id = $(this).data('id');
                var nome = $(this).data('nome');
                $('#id_fonte').val(id);
                $('#nome_fonte').val(nome);
                $('#modalFonte').modal('hide');
            });

        });
    </script>
@endsection