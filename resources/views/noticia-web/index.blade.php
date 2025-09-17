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
                                        <select class="form-control" name="tipo_data" id="tipo_data">
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
                                        <input type="hidden" name="fonte" id="id_fonte" value="{{ ($fonte_web) ? $fonte_web->id : '' }}">
                                        <div class="input-group">
                                            <input type="text" class="form-control" style="height: 40px;" id="nome_fonte" placeholder="Selecione uma fonte" value="{{ ($fonte_web) ? $fonte_web->nome : '' }}" readonly>
                                            <div class="input-group-append">
                                                <button type="button" style="margin: 0px;" class="btn btn-primary" data-toggle="modal" data-target="#modalFonte">Selecionar Fonte</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Cliente</label>
                                        <input type="hidden" name="cliente_selecionado" id="cliente_selecionado" value="{{ ($cliente_selecionado) ? $cliente_selecionado : 0 }}">
                                        <select class="form-control cliente" name="cliente" id="cliente">
                                            <option value="">Selecione um cliente</option>
                                            @foreach ($clientes as $cliente)
                                                <option value="{{ $cliente->id }}" {{ ($cliente_selecionado == $cliente->id) ? 'selected' : '' }}>{{ $cliente->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Área do Cliente</label>
                                        <input type="hidden" name="area_selecionada" id="area_selecionada" value="{{ ($area_selecionada) ? $area_selecionada : 0 }}">
                                        <select class="form-control area" name="cd_area" id="cd_area" disabled>
                                            <option value="0">Selecione uma área</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Sentimento</label>
                                        <select class="form-control" name="sentimento" id="sentimento">
                                            <option value="">Selecione um sentimento</option>
                                            <option value="positivo" {{ ($sentimento == 'positivo') ? 'selected' : '' }}>Positivo</option>
                                            <option value="neutro" {{ ($sentimento == 'neutro') ? 'selected' : '' }}>Neutro</option>
                                            <option value="negativo" {{ ($sentimento == 'negativo') ? 'selected' : '' }}>Negativo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Termo <span class="text-primary">Busca considera título e conteúdo da notícia</span></label>
                                        <input type="text" class="form-control" name="termo" id="termo" minlength="3" placeholder="Termo" value="{{ $termo }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Responsável pelo cadastro</label>
                                        <select class="form-control" name="usuario" id="usuario">
                                            <option value="">Selecione um usuário</option>
                                            <option value="S" {{ ($usuario == 'S') ? 'selected' : '' }}>Sistema</option>
                                            @foreach ($usuarios as $user)
                                                <option value="{{ $user->id }}" {{ ($usuario == $user->id) ? 'selected' : '' }}>{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 checkbox-radios mb-0">
                                    <a href="{{ url('noticia/web/limpar-filtros') }}" class="btn btn-warning btn-limpar mb-3"><i class="fa fa-refresh"></i> Limpar</a>
                                    <button type="submit" id="btn-find" class="btn btn-primary mb-3"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div>
                <div class="col-lg-12 col-sm-12 conteudo">      
                    @if(count($dados))
                        <div class="d-flex justify-content-between align-items-center px-3 mb-3">
                            <h6 class="mb-0">Mostrando {{ $dados->count() }} de {{ $dados->total() }} notícias</h6>
                            <div class="selection-controls">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                    <label class="form-check-label" for="selectAll">
                                        Selecionar todas desta página
                                    </label>
                                </div>
                                <span class="badge badge-info ml-2" id="selectedCount">0 selecionadas</span>
                                <button type="button" class="btn btn-danger btn-sm ml-2" id="deleteSelected" disabled>
                                    <i class="fa fa-trash"></i> Excluir Selecionadas
                                </button>
                            </div>
                        </div>
                        
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
                            <div class="card noticia-card" data-id="{{ $dado->id }}">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 mb-2">
                                            <div class="form-check float-right">
                                                <input class="form-check-input noticia-checkbox" type="checkbox" value="{{ $dado->id }}" id="noticia_{{ $dado->id }}">
                                                <label class="form-check-label text-primary font-weight-bold" for="noticia_{{ $dado->id }}">
                                                    Selecionar notícia
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-2 col-sm-12 img-{{ $dado->id }}" style="max-height: 300px; overflow: hidden;">   
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
                                                <p class="text-muted mb-1"> {!! ($dado->data_noticia) ? date('d/m/Y', strtotime($dado->data_noticia)) : date('d/m/Y', strtotime($dado->data_noticia)) !!} - {{ ($dado->fonte) ? $dado->fonte->nome : '' }} {{ ($dado->id_sessao_web) ? "- ".$dado->secao->ds_sessao : '' }}</p> 
                                                <p class="mb-1">
                                                    <strong>Retorno de Mídia: </strong>{{ ($dado->nu_valor) ? "R$ ".number_format($dado->nu_valor, 2, ',', '.') : 'Não calculado' }}
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
<style>
    .noticia-card.selected {
        border: 2px solid #007bff;
        background-color: #f8f9ff;
    }
    .selection-controls {
        background-color: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #dee2e6;
    }
</style>
    <script>
        $(document).ready(function() {

            var host =  $('meta[name="base-url"]').attr('content');
            var token = $('meta[name="csrf-token"]').attr('content');

            // Função para atualizar contador de selecionadas
            function updateSelectedCount() {
                var count = $('.noticia-checkbox:checked').length;
                $('#selectedCount').text(count + ' selecionadas');
                
                // Habilita/desabilita botão de excluir
                $('#deleteSelected').prop('disabled', count === 0);
                
                // Atualiza estado do checkbox "Selecionar todas"
                var total = $('.noticia-checkbox').length;
                if (count === 0) {
                    $('#selectAll').prop('indeterminate', false).prop('checked', false);
                } else if (count === total) {
                    $('#selectAll').prop('indeterminate', false).prop('checked', true);
                } else {
                    $('#selectAll').prop('indeterminate', true);
                }
            }

            // Checkbox "Selecionar todas"
            $('#selectAll').change(function() {
                var isChecked = $(this).prop('checked');
                $('.noticia-checkbox').prop('checked', isChecked).trigger('change');
            });

            // Checkbox individual da notícia
            $(document).on('change', '.noticia-checkbox', function() {
                var card = $(this).closest('.noticia-card');
                if ($(this).prop('checked')) {
                    card.addClass('selected');
                } else {
                    card.removeClass('selected');
                }
                updateSelectedCount();
            });

            // Botão excluir selecionadas
            $('#deleteSelected').click(function() {
                var selectedIds = [];
                $('.noticia-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length === 0) {
                    alert('Nenhuma notícia selecionada!');
                    return;
                }

                var confirmMessage = 'Tem certeza que deseja excluir ' + selectedIds.length + ' notícia(s) selecionada(s)? Esta ação não pode ser desfeita.';
                
                if (confirm(confirmMessage)) {
                    // Aqui você implementaria a requisição AJAX para excluir
                    $.ajax({
                        url: host + '/noticia/web/excluir-lote',
                        type: 'POST',
                        data: {
                            "_token": token,
                            "ids": selectedIds
                        },
                        beforeSend: function() {
                            $('#deleteSelected').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Excluindo...');
                        },
                        success: function(response) {
                            if (response.success) {
                                alert('Notícias excluídas com sucesso!');
                                location.reload(); // Recarrega a página para atualizar a lista
                            } else {
                                alert('Erro ao excluir notícias: ' + (response.message || 'Erro desconhecido'));
                            }
                        },
                        error: function(xhr) {
                            alert('Erro ao excluir notícias. Por favor, tente novamente.');
                            console.error('Erro:', xhr);
                        },
                        complete: function() {
                            $('#deleteSelected').prop('disabled', false).html('<i class="fa fa-trash"></i> Excluir Selecionadas');
                        }
                    });
                }
            });

            // Inicializa contador
            updateSelectedCount();

            $(document).on('change', '.cliente', function() {
                var cliente = $(this).val();
                buscarAreas(cliente);
            });

            $(".cliente").trigger('change');

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

            function buscarAreas(cliente){

                if(cliente == '') {
                    $('.area').attr('disabled', true);
                    $('.area').append('<option value="">Nenhuma área cadastrada</option>').val('');
                    return;
                }

                $.ajax({
                    url: host+'/api/cliente/getAreasCliente',
                    type: 'GET',
                    data: {
                        "_token": $('meta[name="csrf-token"]').attr('content'),
                        "cliente": cliente,
                    },
                    beforeSend: function() {
                        $('.area').append('<option value="">Carregando...</option>').val('');
                    },
                    success: function(data) {

                        $('.area').find('option').remove();
                        $('.area').attr('disabled', false);

                        if(data.length == 0) {                            
                            $('.area').append('<option value="">Nenhuma área cadastrada</option>').val('');
                            return;
                        }
                                
                        $('.area').append('<option value="">Selecione uma área</option>').val('');
                        data.forEach(element => {
                            let option = new Option(element.descricao, element.id);
                            $('.area').append(option);
                        });  

                        var area_selecionada = $("#area_selecionada").val();

                        if(area_selecionada > 0){
                            $('#cd_area').val(area_selecionada).change();
                        }           
                    },
                    complete: function(){
                                
                    }
                });
            }

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

        $(document).ready(function() {

            
            
        });
    </script>
@endsection