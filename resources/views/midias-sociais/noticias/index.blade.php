@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-newspaper-o ml-3"></i> Mídias Sociais
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Notícias
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('midias-sociais/monitoramentos') }}" class="btn btn-primary pull-right" style="margin-right: 12px;">
                        <i class="fa fa-hashtag"></i> Monitoramentos
                    </a>
                    <a href="{{ url('midias-sociais/posts') }}" class="btn btn-info pull-right mr-1">
                        <i class="fa fa-comments"></i> Posts
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md-12">
                @include('layouts.mensagens')
            </div>
            
            <!-- Estatísticas Rápidas -->
            <div class="col-md-12">
                <div class="alert alert-primary mb-4">
                    <div class="row text-center">
                        <div class="col-md-2">
                            <strong>{{ $estatisticas['total_noticias'] ?? 0 }}</strong>
                            <div><small>Total de Notícias</small></div>
                        </div>
                        <div class="col-md-2">
                            <strong>{{ $estatisticas['noticias_hoje'] ?? 0 }}</strong>
                            <div><small>Notícias Hoje</small></div>
                        </div>
                        <div class="col-md-2">
                            <strong>R$ {{ number_format($estatisticas['valor_total'] ?? 0, 2, ',', '.') }}</strong>
                            <div><small>Valor Total</small></div>
                        </div>
                        <div class="col-md-2">
                            <strong>{{ $estatisticas['clientes_atingidos'] ?? 0 }}</strong>
                            <div><small>Clientes Atingidos</small></div>
                        </div>
                        <div class="col-md-4">
                            <div class="row text-center">
                                <div class="col-3">
                                    <i class="fa fa-twitter text-info"></i>
                                    <div><small>{{ $estatisticas['por_rede']['twitter'] ?? 0 }}</small></div>
                                </div>
                                <div class="col-3">
                                    <i class="fa fa-linkedin text-primary"></i>
                                    <div><small>{{ $estatisticas['por_rede']['linkedin'] ?? 0 }}</small></div>
                                </div>
                                <div class="col-3">
                                    <i class="fa fa-facebook text-primary"></i>
                                    <div><small>{{ $estatisticas['por_rede']['facebook'] ?? 0 }}</small></div>
                                </div>
                                <div class="col-3">
                                    <i class="fa fa-instagram text-danger"></i>
                                    <div><small>{{ $estatisticas['por_rede']['instagram'] ?? 0 }}</small></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filtros -->
            <div class="col-md-12">
                {!! Form::open(['id' => 'frm_filtro_noticias', 'class' => 'form-horizontal', 'url' => ['midias-sociais/noticias']]) !!}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <div class="btn-group" role="group" id="presetsData">
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-preset="hoje">Hoje</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-preset="ontem">Ontem</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-preset="7dias">Últimos 7 dias</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-preset="30dias">Últimos 30 dias</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-preset="mes">Este mês</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><i class="fa fa-filter"></i> Cliente</label>
                                <select class="form-control select2" name="cliente_id" id="cliente_id">
                                    <option value="">Todos os clientes</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                            {{ $cliente->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Rede Social</label>
                                <select class="form-control" name="rede_social" id="rede_social">
                                    <option value="">Todas</option>
                                    <option value="twitter" {{ request('rede_social') == 'twitter' ? 'selected' : '' }}>Twitter</option>
                                    <option value="linkedin" {{ request('rede_social') == 'linkedin' ? 'selected' : '' }}>LinkedIn</option>
                                    <option value="facebook" {{ request('rede_social') == 'facebook' ? 'selected' : '' }}>Facebook</option>
                                    <option value="instagram" {{ request('rede_social') == 'instagram' ? 'selected' : '' }}>Instagram</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Data Inicial</label>
                                <input type="date" class="form-control" name="data_inicial" id="data_inicial" 
                                    value="{{ request('data_inicial', date('Y-m-d', strtotime('-7 days'))) }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Data Final</label>
                                <input type="date" class="form-control" name="data_final" id="data_final" 
                                    value="{{ request('data_final', date('Y-m-d')) }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Palavra-chave</label>
                                <input type="text" class="form-control" name="palavra_chave" id="palavra_chave" 
                                    placeholder="Buscar..." value="{{ request('palavra_chave') }}">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary mt-4 w-100"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Sentimento</label>
                                <select class="form-control" name="sentimento" id="sentimento">
                                    <option value="">Todos</option>
                                    <option value="1" {{ request('sentimento') == '1' ? 'selected' : '' }}>Positivo</option>
                                    <option value="0" {{ request('sentimento') == '0' ? 'selected' : '' }}>Neutro</option>
                                    <option value="-1" {{ request('sentimento') == '-1' ? 'selected' : '' }}>Negativo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Ordenar por</label>
                                <select class="form-control" name="ordenar" id="ordenar">
                                    <option value="data_desc" {{ request('ordenar', 'data_desc') == 'data_desc' ? 'selected' : '' }}>Data (Mais recente)</option>
                                    <option value="data_asc" {{ request('ordenar') == 'data_asc' ? 'selected' : '' }}>Data (Mais antigo)</option>
                                    <option value="valor_desc" {{ request('ordenar') == 'valor_desc' ? 'selected' : '' }}>Valor (Maior)</option>
                                    <option value="titulo_asc" {{ request('ordenar') == 'titulo_asc' ? 'selected' : '' }}>Título (A-Z)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Por página</label>
                                <select class="form-control" name="per_page" id="per_page">
                                    <option value="20" {{ request('per_page', '20') == '20' ? 'selected' : '' }}>20</option>
                                    <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                                </select>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>

            <!-- Resumo e Ações -->
            <div class="border-top p-3 bg-light">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex gap-4 flex-wrap align-items-center">
                        <div>
                            <strong>Total encontradas:</strong>
                            <span class="badge bg-secondary">{{ $noticias->total() ?? 0 }}</span>
                        </div>
                        <div class="ml-2">
                            <strong>Mostrando:</strong>
                            <span class="badge bg-info">{{ $noticias->count() ?? 0 }}</span>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap align-items-center mt-2 mt-md-0">
                        <a href="{{ url('midias-sociais/posts') }}" class="btn btn-info">
                            <i class="fa fa-plus-circle"></i> Criar Mais Notícias
                        </a>
                        <a href="{{ url('midias-sociais/monitoramentos') }}" class="btn btn-primary">
                            <i class="fa fa-hashtag"></i> Gerenciar Monitoramentos
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Listagem de Notícias -->
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    
                    @forelse($noticias as $noticia)
                        <div class="card mb-3 noticia-card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-1 text-center">
                                        <div class="social-icon">
                                            <i class="fa {{ $noticia->rede_icone }} fa-2x text-{{ $noticia->rede_cor }}"></i>
                                        </div>
                                        <small class="text-muted d-block mt-1">{{ ucfirst($noticia->rede_social) }}</small>
                                        <div class="mt-2">
                                            <span class="badge badge-{{ $noticia->sentimento_cor }} badge-pill">
                                                {{ $noticia->sentimento_texto }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-11">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="flex-grow-1">
                                                <h5 class="mb-1">
                                                    {{ $noticia->titulo }}
                                                    @if($noticia->valor_retorno > 0)
                                                        <span class="badge badge-success ml-2">
                                                            R$ {{ number_format($noticia->valor_retorno, 2, ',', '.') }}
                                                        </span>
                                                    @endif
                                                </h5>
                                                <p class="text-muted mb-1">
                                                    <strong>Autor:</strong> {{ $noticia->autor_display }}
                                                    • <strong>Publicado em:</strong> {{ $noticia->data_publicacao->format('d/m/Y \à\s H:i') }}
                                                    • <strong>Notícia criada em:</strong> {{ $noticia->created_at->format('d/m/Y \à\s H:i') }}
                                                    @if($noticia->usuario)
                                                        por {{ $noticia->usuario->name }}
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                @if($noticia->clientes->count() > 0)
                                                    @foreach($noticia->clientes as $cliente)
                                                        <span class="badge badge-info mb-1 d-block">{{ $cliente->nome }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="badge badge-warning">Sem cliente vinculado</span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="noticia-content mb-2">
                                            <p class="mb-1">{{ Str::limit($noticia->resumo ?: 'Sem conteúdo disponível.', 300) }}</p>
                                        </div>
                                        
                                        <!-- Métricas de Engagement -->
                                        @if($noticia->metricas_engagement)
                                            <div class="engagement-metrics mb-2">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <small class="text-muted mr-3">
                                                            <i class="fa fa-heart text-danger"></i> {{ number_format($noticia->metricas_engagement['likes'] ?? 0) }} curtidas
                                                        </small>
                                                        <small class="text-muted mr-3">
                                                            <i class="fa fa-share text-success"></i> {{ number_format($noticia->metricas_engagement['shares'] ?? 0) }} compartilhamentos
                                                        </small>
                                                        <small class="text-muted mr-3">
                                                            <i class="fa fa-comment text-primary"></i> {{ number_format($noticia->metricas_engagement['comentarios'] ?? 0) }} comentários
                                                        </small>
                                                        @if(isset($noticia->metricas_engagement['views']) && $noticia->metricas_engagement['views'] > 0)
                                                            <small class="text-muted mr-3">
                                                                <i class="fa fa-eye text-info"></i> {{ number_format($noticia->metricas_engagement['views']) }} visualizações
                                                            </small>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-4 text-right">
                                                        @if($noticia->url_post)
                                                            <a href="{{ $noticia->url_post }}" target="_blank" class="btn btn-sm btn-outline-primary mr-1">
                                                                <i class="fa fa-external-link"></i> Post Original
                                                            </a>
                                                        @endif
                                                        @if($noticia->postOriginal)
                                                            <button class="btn btn-sm btn-outline-info mr-1" onclick="verDetalhesPost({{ $noticia->postOriginal->id }})">
                                                                <i class="fa fa-info-circle"></i> Post Coleta
                                                            </button>
                                                        @endif
                                                        <button class="btn btn-sm btn-outline-danger" onclick="removerNoticia({{ $noticia->id }})">
                                                            <i class="fa fa-trash"></i> Remover
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <!-- Informações Adicionais -->
                                        @if(isset($noticia->misc_data['hashtags']) && count($noticia->misc_data['hashtags']) > 0)
                                            <div class="hashtags-section mb-2">
                                                <small class="text-muted font-weight-bold mr-2">
                                                    <i class="fa fa-hashtag"></i> Hashtags:
                                                </small>
                                                @foreach($noticia->misc_data['hashtags'] as $hashtag)
                                                    <span class="badge badge-pill badge-primary mr-1">{{ $hashtag }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                        
                                        @if(isset($noticia->misc_data['mencoes']) && count($noticia->misc_data['mencoes']) > 0)
                                            <div class="mentions-section">
                                                <small class="text-muted font-weight-bold mr-2">
                                                    <i class="fa fa-at"></i> Menções:
                                                </small>
                                                @foreach($noticia->misc_data['mencoes'] as $mencao)
                                                    <span class="badge badge-pill badge-secondary mr-1">{{ $mencao }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info text-center">
                            <i class="fa fa-info-circle fa-2x mb-2"></i><br>
                            <strong>Nenhuma notícia encontrada</strong><br>
                            Não há notícias criadas para os filtros selecionados.
                            <div class="mt-3">
                                <a href="{{ url('midias-sociais/posts') }}" class="btn btn-primary">
                                    <i class="fa fa-plus-circle"></i> Criar Notícias a partir dos Posts
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Paginação -->
            <div class="row">
                <div class="col-md-12 text-center">
                    {{ $noticias->onEachSide(1)->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Select2 para campos de seleção
        $('.select2').select2({
            placeholder: 'Selecione...',
            allowClear: true
        });
        
        // Auto-submit do formulário quando filtros mudam
        $('#per_page, #ordenar').on('change', function() {
            $('#frm_filtro_noticias').submit();
        });
        
        // Preset de datas
        $('#presetsData button').on('click', function() {
            let preset = $(this).data('preset');
            let hoje = moment();
            let dt_inicial = '';
            let dt_final = '';

            switch(preset) {
                case 'hoje':
                    dt_inicial = hoje.format('YYYY-MM-DD');
                    dt_final = hoje.format('YYYY-MM-DD');
                    break;
                case 'ontem':
                    dt_inicial = hoje.clone().subtract(1, 'days').format('YYYY-MM-DD');
                    dt_final = hoje.clone().subtract(1, 'days').format('YYYY-MM-DD');
                    break;
                case '7dias':
                    dt_inicial = hoje.clone().subtract(6, 'days').format('YYYY-MM-DD');
                    dt_final = hoje.format('YYYY-MM-DD');
                    break;
                case '30dias':
                    dt_inicial = hoje.clone().subtract(29, 'days').format('YYYY-MM-DD');
                    dt_final = hoje.format('YYYY-MM-DD');
                    break;
                case 'mes':
                    dt_inicial = hoje.clone().startOf('month').format('YYYY-MM-DD');
                    dt_final = hoje.format('YYYY-MM-DD');
                    break;
            }

            $('#data_inicial').val(dt_inicial);
            $('#data_final').val(dt_final);
        });
    });
    
    function verDetalhesPost(postId) {
        var host = $('meta[name="base-url"]').attr('content');
        
        $('#modalDetalhesPost').modal('show');
        $('#conteudoDetalhesPost').html(`
            <div class="text-center p-4">
                <i class="fa fa-spinner fa-spin fa-2x text-primary"></i>
                <p class="mt-2">Carregando detalhes do post original...</p>
            </div>
        `);
        
        $.ajax({
            url: host + '/midias-sociais/posts/' + postId + '/detalhes',
            type: 'GET',
            dataType: 'html',
            success: function(response) {
                $('#conteudoDetalhesPost').html(response);
            },
            error: function(xhr, status, error) {
                console.error('Erro ao carregar detalhes:', error);
                $('#conteudoDetalhesPost').html(`
                    <div class="alert alert-danger text-center">
                        <i class="fa fa-exclamation-triangle"></i>
                        <strong>Erro ao carregar detalhes</strong><br>
                        <small>Verifique sua conexão e tente novamente.</small>
                    </div>
                `);
            }
        });
    }
    
    function removerNoticia(noticiaId) {
        var host = $('meta[name="base-url"]').attr('content');
        var token = $('meta[name="csrf-token"]').attr('content');
        
        Swal.fire({
            title: 'Remover Notícia',
            text: 'Esta ação não pode ser desfeita. A notícia será removida e o post original será marcado como não processado.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, remover!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: host + '/midias-sociais/noticias/' + noticiaId + '/remover',
                    type: 'DELETE',
                    data: {
                        "_token": token
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Removida!', response.message, 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Erro', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Erro', 'Não foi possível remover a notícia', 'error');
                    }
                });
            }
        });
    }
</script>

<!-- Modal de Detalhes do Post -->
<div class="modal fade" id="modalDetalhesPost" tabindex="-1" role="dialog" aria-labelledby="modalDetalhesPostLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetalhesPostLabel">
                    <i class="fa fa-info-circle"></i> Detalhes do Post Original
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="conteudoDetalhesPost">
                <div class="text-center">
                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                    <p>Carregando detalhes...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<style>
    .noticia-card {
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .noticia-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .social-icon i {
        transition: transform 0.2s ease;
    }
    
    .social-icon:hover i {
        transform: scale(1.1);
    }
    
    .engagement-metrics {
        border-left: 3px solid #007bff;
        padding-left: 10px;
        margin-bottom: 10px;
        background: linear-gradient(90deg, rgba(0,123,255,0.05) 0%, transparent 100%);
        border-radius: 0 8px 8px 0;
        padding: 10px 0 10px 15px;
    }
    
    .hashtags-section, .mentions-section {
        background-color: #f8f9fa;
        padding: 8px 12px;
        border-radius: 6px;
    }
    
    .badge:hover {
        transform: scale(1.05);
        transition: transform 0.1s ease;
    }
    
    /* Responsividade para mobile */
    @media (max-width: 768px) {
        .engagement-metrics .col-md-8,
        .engagement-metrics .col-md-4 {
            text-align: center;
            margin-bottom: 10px;
        }
        
        .d-flex.gap-2 {
            flex-direction: column;
            align-items: stretch;
        }
        
        .d-flex.gap-2 .btn {
            margin-bottom: 5px;
        }
    }
</style>
@endsection
