@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">
                        <i class="fa fa-comments ml-3"></i> Mídias Sociais
                        <i class="fa fa-angle-double-right" aria-hidden="true"></i> Posts Coletados
                    </h4>
                </div>
                <div class="col-md-4">
                    <a href="{{ url('midias-sociais/monitoramentos') }}" class="btn btn-primary pull-right" style="margin-right: 12px;">
                        <i class="fa fa-hashtag"></i> Monitoramentos
                    </a>
                    <a href="{{ url('midias-sociais/noticias') }}" class="btn btn-info pull-right mr-1">
                        <i class="fa fa-newspaper-o"></i> Notícias
                    </a>
                    <a href="#" class="btn btn-success pull-right mr-1" onclick="exportarPosts()">
                        <i class="fa fa-download"></i> Exportar
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
                <div class="alert alert-info mb-4">
                    <div class="row text-center">
                        <div class="col-md-2">
                            <strong>{{ $estatisticas['total_posts'] ?? 0 }}</strong>
                            <div><small>Total de Posts</small></div>
                        </div>
                        <div class="col-md-2">
                            <strong>{{ $estatisticas['posts_hoje'] ?? 0 }}</strong>
                            <div><small>Posts Hoje</small></div>
                        </div>
                        <div class="col-md-2">
                            <strong>{{ number_format($estatisticas['total_likes'] ?? 0) }}</strong>
                            <div><small>Total Curtidas</small></div>
                        </div>
                        <div class="col-md-2">
                            <strong>{{ number_format($estatisticas['total_shares'] ?? 0) }}</strong>
                            <div><small>Compartilhamentos</small></div>
                        </div>
                        <div class="col-md-2">
                            <strong>{{ number_format($estatisticas['total_comentarios'] ?? 0) }}</strong>
                            <div><small>Comentários</small></div>
                        </div>
                        <div class="col-md-2">
                            <strong>{{ $estatisticas['monitoramentos_ativos'] ?? 0 }}</strong>
                            <div><small>Monitoramentos Ativos</small></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filtros -->
            <div class="col-md-12">
                {!! Form::open(['id' => 'frm_filtro_posts', 'class' => 'form-horizontal', 'url' => ['midias-sociais/posts']]) !!}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <div class="btn-group" role="group" id="presetsData">
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-preset="hoje">Hoje</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-preset="ontem">Ontem</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-preset="7dias">Últimos 7 dias</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-preset="30dias">Últimos 30 dias</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-preset="mes">Este mês</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-preset="mesanterior">Mês anterior</button>
                                </div>
                            </div>
                        </div>
                    <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><i class="fa fa-filter"></i> Monitoramento</label>
                                        <select class="form-control select2" name="monitoramento_id" id="monitoramento_id">
                                            <option value="">Todos os monitoramentos</option>
                                            @foreach($monitoramentos as $monitoramento)
                                                <option value="{{ $monitoramento->id }}" {{ request('monitoramento_id') == $monitoramento->id ? 'selected' : '' }}>
                                                    {{ $monitoramento->nome }} ({{ $monitoramento->cliente->nome }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Rede Social</label>
                                        <select class="form-control" name="tipo_midia" id="tipo_midia">
                                            <option value="">Todas</option>
                                            <option value="twitter" {{ request('tipo_midia') == 'twitter' ? 'selected' : '' }}>Twitter</option>
                                            <option value="linkedin" {{ request('tipo_midia') == 'linkedin' ? 'selected' : '' }}>LinkedIn</option>
                                            <option value="facebook" {{ request('tipo_midia') == 'facebook' ? 'selected' : '' }}>Facebook</option>
                                            <option value="instagram" {{ request('tipo_midia') == 'instagram' ? 'selected' : '' }}>Instagram</option>
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
                                            placeholder="Ex: exemplo" value="{{ request('palavra_chave') }}">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary mt-4 w-100"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>  
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Min. Curtidas</label>
                                        <input type="number" class="form-control" name="min_likes" id="min_likes" min="0" placeholder="0" value="{{ request('min_likes') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Min. Shares</label>
                                        <input type="number" class="form-control" name="min_shares" id="min_shares" min="0" placeholder="0" value="{{ request('min_shares') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Relevância</label>
                                        <select class="form-control" name="relevancia" id="relevancia">
                                            <option value="">Qualquer</option>
                                            <option value="alta" {{ request('relevancia') == 'alta' ? 'selected' : '' }}>Alta (&gt;70%)</option>
                                            <option value="media" {{ request('relevancia') == 'media' ? 'selected' : '' }}>Média (30-70%)</option>
                                            <option value="baixa" {{ request('relevancia') == 'baixa' ? 'selected' : '' }}>Baixa (&lt;30%)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Com Mídia</label>
                                        <select class="form-control" name="com_midia" id="com_midia">
                                            <option value="">Todos</option>
                                            <option value="imagem" {{ request('com_midia') == 'imagem' ? 'selected' : '' }}>Com Imagem</option>
                                            <option value="video" {{ request('com_midia') == 'video' ? 'selected' : '' }}>Com Vídeo</option>
                                            <option value="sem_midia" {{ request('com_midia') == 'sem_midia' ? 'selected' : '' }}>Sem Mídia</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Ordenar por</label>
                                        <select class="form-control" name="ordenar" id="ordenar">
                                            <option value="data_desc" {{ request('ordenar', 'data_desc') == 'data_desc' ? 'selected' : '' }}>Data (Mais recente)</option>
                                            <option value="data_asc" {{ request('ordenar') == 'data_asc' ? 'selected' : '' }}>Data (Mais antigo)</option>
                                            <option value="likes_desc" {{ request('ordenar') == 'likes_desc' ? 'selected' : '' }}>Curtidas (Maior)</option>
                                            <option value="relevancia_desc" {{ request('ordenar') == 'relevancia_desc' ? 'selected' : '' }}>Relevância (Maior)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Por página</label>
                                        <select class="form-control" name="per_page" id="per_page">
                                            <option value="20" {{ request('per_page') == '20' ? 'selected' : '' }}>20</option>
                                            <option value="50" {{ request('per_page', '50') == '50' ? 'selected' : '' }}>50</option>
                                            <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                                        </select>
                                    </div>
                                </div>
                                </div>
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
                            <strong>Total encontrados:</strong>
                            <span class="badge bg-secondary">{{ $posts->total() ?? 0 }}</span>
                        </div>
                        <div class="ml-2">
                            <strong>Mostrando:</strong>
                            <span class="badge bg-info">{{ $posts->count() ?? 0 }}</span>
                        </div>
                        <div class="ml-2" id="selection-info" style="display: none;">
                            <strong>Selecionados:</strong>
                            <span class="badge badge-warning" id="selected-count">0</span>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap align-items-center mt-2 mt-md-0">
                        <button type="button" class="btn btn-orange" id="btn-criar-noticias" style="display: none;" onclick="criarNoticiasDosPosts()">
                            <i class="fa fa-newspaper-o"></i> Criar Notícias
                        </button>
                        <a href="#" class="btn btn-success" onclick="exportarPosts()">
                            <i class="fa fa-download"></i> Exportar Posts
                        </a>
                        <a href="{{ url('midias-sociais/noticias') }}" class="btn btn-info">
                            <i class="fa fa-newspaper-o"></i> Ver Notícias
                        </a>
                        <a href="{{ url('midias-sociais/monitoramentos') }}" class="btn btn-primary">
                            <i class="fa fa-hashtag"></i> Gerenciar Monitoramentos
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Controles de Seleção -->
            @if($posts->count() > 0)
            <div class="border-bottom p-2 bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <label class="form-check-label mb-0">
                            <input type="checkbox" id="select-all" class="form-check-input">
                            <span class="form-check-sign"></span>
                            <strong>Selecionar todos os posts desta página</strong>
                        </label>
                    </div>
                    <div>
                        <small class="text-muted">Selecione os posts que deseja converter em notícias</small>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Listagem de Posts -->
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    
                    @forelse($posts as $post)
                        <div class="card mb-3 post-card" data-post-id="{{ $post->id }}">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-1 text-center position-relative">
                                        <!-- Checkbox de seleção -->
                                        <div class="post-selection-checkbox">
                                            <label class="form-check-label">
                                                <input type="checkbox" 
                                                       class="form-check-input post-checkbox" 
                                                       value="{{ $post->id }}" 
                                                       data-processed="{{ $post->processado ? '1' : '0' }}">
                                                <span class="form-check-sign"></span>
                                            </label>
                                        </div>
                                        
                                        <!-- Ícone da rede social -->
                                        <div class="social-icon-container">
                                            <div class="social-icon">
                                                @switch($post->tipo_midia)
                                                    @case('twitter')
                                                        <i class="fa fa-twitter fa-2x text-info"></i>
                                                        @break
                                                    @case('linkedin')
                                                        <i class="fa fa-linkedin fa-2x text-primary"></i>
                                                        @break
                                                    @case('facebook')
                                                        <i class="fa fa-facebook fa-2x text-primary"></i>
                                                        @break
                                                    @case('instagram')
                                                        <i class="fa fa-instagram fa-2x text-danger"></i>
                                                        @break
                                                    @default
                                                        <i class="fa fa-share-alt fa-2x text-secondary"></i>
                                                @endswitch
                                            </div>
                                            <small class="text-muted d-block mt-1">{{ ucfirst($post->tipo_midia) }}</small>
                                        </div>
                                        
                                        <!-- Status processado -->
                                        @if($post->processado)
                                            <div class="processed-indicator">
                                                <small class="text-success d-block mt-2">
                                                    <i class="fa fa-check-circle"></i> Processado
                                                </small>
                                            </div>
                                        @endif
                                        
                                        <!-- Indicador se já tem notícia -->
                                        @if(method_exists($post, 'getTemNoticiaAttribute') && $post->tem_noticia)
                                            <div class="news-indicator">
                                                <small class="text-primary d-block mt-1">
                                                    <i class="fa fa-newspaper-o"></i> Notícia Criada
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="col-md-11">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                             <div class="d-flex align-items-center">
                                                 @php
                                                     // Prioridade 1: Foto real do misc_data (Twitter)
                                                     $profilePicUrl = null;
                                                     
                                                     if($post->profile_pic_url) {
                                                         $profilePicUrl = $post->profile_pic_url;
                                                     } elseif($post->autor_username && $post->tipo_midia == 'twitter') {
                                                         // Para Twitter, usar Twivatar (API gratuita para avatares) como fallback
                                                         $username = ltrim($post->autor_username, '@');
                                                         $profilePicUrl = "https://twivatar.glitch.me/{$username}";
                                                     } elseif($post->autor_id) {
                                                         // Para outras redes, usar API genérica ou placeholder
                                                         $profilePicUrl = "https://ui-avatars.com/api/?name=" . urlencode($post->autor_display) . "&size=96&background=667eea&color=fff";
                                                     }
                                                 @endphp
                                                 
                                                 
                                                 @if($profilePicUrl)
                                                     <div class="profile-pic-container mr-3">
                                                         <img src="{{ $profilePicUrl }}" 
                                                              alt="Foto de perfil de {{ $post->autor_display }}" 
                                                              class="profile-pic profile-pic-clickable"
                                                              title="Clique para ampliar a foto de perfil"
                                                              onclick="abrirImagem('{{ $profilePicUrl }}')"
                                                              onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                         <div class="profile-pic-fallback" style="display: none;">
                                                             <i class="fa fa-user"></i>
                                                         </div>
                                                     </div>
                                                 @else
                                                     <!-- Fallback padrão quando não temos dados para foto -->
                                                     <div class="profile-pic-container mr-3">
                                                         <div class="profile-pic-fallback">
                                                             @switch($post->tipo_midia)
                                                                 @case('twitter')
                                                                     <i class="fa fa-twitter"></i>
                                                                     @break
                                                                 @case('linkedin')
                                                                     <i class="fa fa-linkedin"></i>
                                                                     @break
                                                                 @case('facebook')
                                                                     <i class="fa fa-facebook"></i>
                                                                     @break
                                                                 @case('instagram')
                                                                     <i class="fa fa-instagram"></i>
                                                                     @break
                                                                 @default
                                                                     <i class="fa fa-user"></i>
                                                             @endswitch
                                                         </div>
                                                     </div>
                                                 @endif
                                                <div>
                                                    <h6 class="mb-0">
                                                        <strong>{{ $post->autor_display }}</strong>
                                                        @if($post->autor_nome && $post->autor_username)
                                                            <span class="text-muted">• {{ $post->autor_nome }}</span>
                                                        @endif
                                                        @if($post->idioma)
                                                            <span class="badge badge-pill badge-light ml-2">
                                                                <i class="fa fa-language"></i> {{ strtoupper($post->idioma) }}
                                                            </span>
                                                        @endif
                                                    </h6>
                                                <small class="text-muted">
                                                    @if($post->data_publicacao)
                                                        {{ $post->data_publicacao->format('d/m/Y \à\s H:i') }}
                                                    @else
                                                        Data não informada
                                                    @endif
                                                    @if($post->data_coleta)
                                                        • Coletado em {{ $post->data_coleta->format('d/m/Y \à\s H:i') }}
                                                    @endif
                                                </small>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                @if($post->monitoramento)
                                                    <span class="badge badge-success">{{ $post->monitoramento->nome }}</span>
                                                    <br>
                                                    @if($post->monitoramento->cliente)
                                                        <small class="text-muted">Cliente: {{ $post->monitoramento->cliente->nome }}</small>
                                                        <br>
                                                    @endif
                                                @endif
                                                @if($post->relevancia_score)
                                                    <div class="relevancia-score">
                                                        <span class="badge badge-{{ $post->relevancia_score >= 0.7 ? 'success' : ($post->relevancia_score >= 0.4 ? 'warning' : 'secondary') }}">
                                                            Relevância: {{ $post->relevancia_percentual }}%
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="post-content mb-2">
                                            <p class="mb-1">{{ Str::limit($post->texto ?: 'Sem conteúdo de texto disponível.', 300) }}</p>
                                        </div>
                                        
                                        <!-- Hashtags e Menções -->
                                        @if($post->hashtags || $post->mencoes)
                                            <div class="post-tags mb-3">
                                                @if($post->hashtags && count($post->hashtags) > 0)
                                                    <div class="mb-2">
                                                        <small class="text-muted font-weight-bold mr-2">
                                                            <i class="fa fa-hashtag"></i> Hashtags:
                                                        </small>
                                                        @foreach($post->hashtags as $hashtag)
                                                            <span class="badge badge-pill badge-primary mr-1" 
                                                                  style="cursor: pointer;" 
                                                                  onclick="filtrarHashtag('{{ trim($hashtag, '#') }}')">
                                                                {{ $hashtag }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                @if($post->mencoes && count($post->mencoes) > 0)
                                                    <div class="mb-2">
                                                        <small class="text-muted font-weight-bold mr-2">
                                                            <i class="fa fa-at"></i> Menções:
                                                        </small>
                                                        @foreach($post->mencoes as $mencao)
                                                            @php
                                                                $username = trim($mencao, '@');
                                                                $socialUrl = '';
                                                                switch($post->tipo_midia) {
                                                                    case 'twitter':
                                                                        $socialUrl = 'https://twitter.com/' . $username;
                                                                        break;
                                                                    case 'instagram':
                                                                        $socialUrl = 'https://instagram.com/' . $username;
                                                                        break;
                                                                    case 'facebook':
                                                                        $socialUrl = 'https://facebook.com/' . $username;
                                                                        break;
                                                                    case 'linkedin':
                                                                        $socialUrl = 'https://linkedin.com/in/' . $username;
                                                                        break;
                                                                }
                                                            @endphp
                                                            @if($socialUrl)
                                                                <a href="{{ $socialUrl }}" target="_blank" 
                                                                   class="badge badge-pill badge-secondary mr-1 text-decoration-none" 
                                                                   style="cursor: pointer;"
                                                                   title="Ver perfil de {{ $mencao }}">
                                                                    <i class="fa fa-external-link-square fa-xs"></i> {{ $mencao }}
                                                                </a>
                                                            @else
                                                                <span class="badge badge-pill badge-secondary mr-1">{{ $mencao }}</span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                        
                                        <!-- Métricas do Post -->
                                        <div class="post-metrics">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <small class="text-muted mr-3">
                                                        <i class="fa fa-heart text-danger"></i> {{ number_format($post->likes ?? 0) }} curtidas
                                                    </small>
                                                    <small class="text-muted mr-3">
                                                        <i class="fa fa-share text-success"></i> {{ number_format($post->shares ?? 0) }} compartilhamentos
                                                    </small>
                                                    <small class="text-muted mr-3">
                                                        <i class="fa fa-comment text-primary"></i> {{ number_format($post->comentarios ?? 0) }} comentários
                                                    </small>
                                                    @if($post->views)
                                                        <small class="text-muted mr-3">
                                                            <i class="fa fa-eye text-info"></i> {{ number_format($post->views) }} visualizações
                                                        </small>
                                                    @endif
                                                </div>
                                                <div class="col-md-4 text-right">
                                                    @if($post->url_post)
                                                        <a href="{{ $post->url_post }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="fa fa-external-link"></i> Ver Original
                                                        </a>
                                                    @endif
                                                    <button class="btn btn-sm btn-outline-info" onclick="verDetalhes({{ $post->id }})">
                                                        <i class="fa fa-info-circle"></i> Detalhes
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        <!-- Mídia do Post -->
                                        @if($post->urls_midia && count($post->urls_midia) > 0)
                                            <div class="post-media mt-3 p-3 bg-light rounded">
                                                <div class="d-flex align-items-center mb-3">
                                                    <h6 class="mb-0 mr-3">
                                                        <i class="fa fa-paperclip text-primary"></i> Mídia Anexa
                                                    </h6>
                                                    <div>
                                                        @if($post->tem_imagem)
                                                            <span class="badge badge-success mr-1">
                                                                <i class="fa fa-image"></i> Imagens
                                                            </span>
                                                        @endif
                                                        @if($post->tem_video)
                                                            <span class="badge badge-danger mr-1">
                                                                <i class="fa fa-video-camera"></i> Vídeos
                                                            </span>
                                                        @endif
                                                        @php
                                                            // Processar mídia de forma inteligente primeiro para poder contar
                                                            $imagens = [];
                                                            $videosAgrupados = [];
                                                            $outros = [];
                                                            
                                                            // Primeiro, identificar e agrupar thumbnails com vídeos
                                                            $thumbnails = [];
                                                            $videosRaw = [];
                                                            $imagensIndependentes = [];
                                                    
                                                    foreach($post->urls_midia as $url) {
                                                        // Detectar imagens: extensões explícitas OU padrões específicos de plataformas
                                                        $isImage = Str::contains(strtolower($url), ['jpg', 'jpeg', 'png', 'gif', 'webp']) ||
                                                                   (Str::contains($url, 'media.licdn.com') && Str::contains($url, '/image/')) ||
                                                                   (Str::contains($url, 'scontent') && Str::contains($url, ['_s.', '_n.', '_b.'])) ||
                                                                   (Str::contains($url, 'fbcdn.net') && Str::contains($url, '/photos/'));
                                                        
                                                        if($isImage) {
                                                            // Verificar se é thumbnail de vídeo (padrões Twitter/LinkedIn/Facebook)
                                                            $isVideoThumbnail = false;
                                                            
                                                            // Padrões Twitter
                                                            if(Str::contains($url, ['amplify_video_thumb', 'video_thumb', 'thumbnail'])) {
                                                                $isVideoThumbnail = true;
                                                            }
                                                            // Padrões LinkedIn - imagens de feed que podem ser thumbnails
                                                            elseif(Str::contains($url, ['media.licdn.com/dms/image']) && Str::contains($url, 'feedshare')) {
                                                                // LinkedIn: assumir que imagens de feedshare podem ser independentes
                                                                // A menos que haja vídeos correspondentes
                                                                $isVideoThumbnail = false; // Trataremos depois no agrupamento
                                                            }
                                                            // Padrões Facebook
                                                            elseif(Str::contains($url, ['scontent', 'fbcdn']) && Str::contains($url, ['_s.', '_t.', 'thumb'])) {
                                                                $isVideoThumbnail = true;
                                                            }
                                                            
                                                            if($isVideoThumbnail) {
                                                                $thumbnails[] = $url;
                                                            } else {
                                                                $imagensIndependentes[] = $url;
                                                            }
                                                        } elseif(Str::contains(strtolower($url), ['mp4', 'webm', 'm3u8'])) {
                                                            $videosRaw[] = $url;
                                                        } elseif(Str::contains(strtolower($url), ['avi', 'mov', 'wmv', 'flv'])) {
                                                            $outros[] = $url;
                                                        }
                                                    }
                                                    
                                                    // Agrupar vídeos por ID (extrair ID comum dos URLs)
                                                    $gruposVideos = [];
                                                    foreach($videosRaw as $videoUrl) {
                                                        $videoId = null;
                                                        
                                                        // Padrões Twitter: amplify_video/ID/
                                                        if(preg_match('/amplify_video\/(\d+)\//', $videoUrl, $matches)) {
                                                            $videoId = $matches[1];
                                                        } 
                                                        // Padrão genérico: video/ID/
                                                        elseif(preg_match('/video\/(\d+)\//', $videoUrl, $matches)) {
                                                            $videoId = $matches[1];
                                                        }
                                                        // Padrões LinkedIn: extrair ID da URL
                                                        elseif(Str::contains($videoUrl, 'licdn.com')) {
                                                            // LinkedIn pode ter IDs em diferentes formatos
                                                            if(preg_match('/\/([A-Za-z0-9_-]{10,})\//', $videoUrl, $matches)) {
                                                                $videoId = $matches[1];
                                                            } elseif(preg_match('/\/(\d{10,})/', $videoUrl, $matches)) {
                                                                $videoId = $matches[1];
                                                            }
                                                        }
                                                        // Padrões Facebook/Instagram
                                                        elseif(Str::contains($videoUrl, ['facebook.com', 'fbcdn', 'instagram.com', 'cdninstagram'])) {
                                                            if(preg_match('/\/(\d{10,})/', $videoUrl, $matches)) {
                                                                $videoId = $matches[1];
                                                            }
                                                        }
                                                        
                                                        // Se não conseguir extrair ID, usar hash do URL
                                                        if(!$videoId) {
                                                            $videoId = md5($videoUrl);
                                                        }
                                                        
                                                        if(!isset($gruposVideos[$videoId])) {
                                                            $gruposVideos[$videoId] = [];
                                                        }
                                                        $gruposVideos[$videoId][] = $videoUrl;
                                                    }
                                                    
                                                    // Para cada grupo de vídeos, escolher a melhor qualidade
                                                    foreach($gruposVideos as $videoId => $urlsDoVideo) {
                                                        $melhorVideo = null;
                                                        $melhorQualidade = 0;
                                                        
                                                        foreach($urlsDoVideo as $videoUrl) {
                                                            // Priorizar MP4 sobre M3U8
                                                            if(Str::contains($videoUrl, '.mp4')) {
                                                                // Extrair resolução do URL (ex: 720x1280)
                                                                if(preg_match('/(\d+)x(\d+)/', $videoUrl, $matches)) {
                                                                    $qualidade = intval($matches[2]); // Usar altura como referência
                                                                    if($qualidade > $melhorQualidade) {
                                                                        $melhorQualidade = $qualidade;
                                                                        $melhorVideo = $videoUrl;
                                                                    }
                                                                } elseif(!$melhorVideo) {
                                                                    $melhorVideo = $videoUrl;
                                                                }
                                                            } elseif(!$melhorVideo && Str::contains($videoUrl, 'm3u8')) {
                                                                $melhorVideo = $videoUrl; // M3U8 como fallback
                                                            }
                                                        }
                                                        
                                                        if($melhorVideo) {
                                                            // Procurar thumbnail correspondente
                                                            $thumbnailCorrespondente = null;
                                                            
                                                            // Para LinkedIn, pode não haver thumbnails explícitos
                                                            // Vamos verificar se há imagens que podem servir como thumbnail
                                                            if(Str::contains($melhorVideo, 'licdn.com') && empty($thumbnails)) {
                                                                // Para LinkedIn, usar a primeira imagem como thumbnail se disponível
                                                                if(!empty($imagensIndependentes)) {
                                                                    foreach($imagensIndependentes as $index => $imagem) {
                                                                        if(Str::contains($imagem, 'licdn.com')) {
                                                                            $thumbnailCorrespondente = $imagem;
                                                                            // Remover da lista de imagens independentes
                                                                            unset($imagensIndependentes[$index]);
                                                                            break;
                                                                        }
                                                                    }
                                                                    // Reindexar array
                                                                    $imagensIndependentes = array_values($imagensIndependentes);
                                                                }
                                                            } else {
                                                                // Lógica original para Twitter e outras redes
                                                                foreach($thumbnails as $thumb) {
                                                                    if(Str::contains($thumb, $videoId)) {
                                                                        $thumbnailCorrespondente = $thumb;
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                            
                                                            $videosAgrupados[] = [
                                                                'video' => $melhorVideo,
                                                                'thumbnail' => $thumbnailCorrespondente,
                                                                'qualidade' => $melhorQualidade ?: 'desconhecida'
                                                            ];
                                                        }
                                                    }
                                                    
                                                    // Imagens finais são as independentes
                                                    $imagens = $imagensIndependentes;
                                                    
                                                    // Para LinkedIn, se não há vídeos mas há imagens, todas devem ser tratadas como imagens independentes
                                                    if($post->tipo_midia === 'linkedin' && empty($videosAgrupados) && !empty($thumbnails)) {
                                                        $imagens = array_merge($imagens, $thumbnails);
                                                    }
                                                @endphp
                                                        <span class="badge badge-info">
                                                            {{ count($imagens) + count($videosAgrupados) + count($outros) }} mídia{{ count($imagens) + count($videosAgrupados) + count($outros) > 1 ? 's' : '' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                <!-- Imagens -->
                                                @if(count($imagens) > 0)
                                                    <div class="media-section mb-4">
                                                        <div class="d-flex align-items-center mb-3">
                                                            <i class="fa fa-image text-success fa-lg mr-2"></i>
                                                            <span class="font-weight-bold text-dark">
                                                                Imagens ({{ count($imagens) }})
                                                            </span>
                                                            <span class="badge badge-success ml-2">Fotos</span>
                                                        </div>
                                                        <div class="row">
                                                            @foreach(array_slice($imagens, 0, 8) as $index => $imagem)
                                                                <div class="col-lg-3 col-md-4 col-6 mb-3">
                                                                    <div class="image-container-modern position-relative">
                                                                        <img src="{{ $imagem }}" 
                                                                             class="image-modern img-fluid" 
                                                                             alt="Imagem {{ $index + 1 }} do post" 
                                                                             onclick="abrirImagem('{{ $imagem }}')"
                                                                             loading="lazy">
                                                                        
                                                                        <!-- Overlay hover -->
                                                                        <div class="image-overlay-modern">
                                                                            <div class="image-overlay-content">
                                                                                <i class="fa fa-search-plus fa-2x text-white"></i>
                                                                                <small class="d-block text-white mt-2">Ampliar</small>
                                                                            </div>
                                                                        </div>
                                                                        
                                                                        <!-- Número da imagem -->
                                                                        <div class="image-number">
                                                                            <span class="badge badge-dark">{{ $index + 1 }}</span>
                                                                        </div>
                                                                        
                                                                        <!-- Efeito shimmer -->
                                                                        <div class="image-shimmer-effect"></div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                            @if(count($imagens) > 8)
                                                                <div class="col-lg-3 col-md-4 col-6 mb-3">
                                                                    <div class="more-images-container position-relative" 
                                                                         onclick="verTodasImagens({{ $post->id }})">
                                                                        <div class="more-images-content">
                                                                            <i class="fa fa-images fa-3x text-white mb-2"></i>
                                                                            <div class="text-white text-center">
                                                                                <strong>+{{ count($imagens) - 8 }}</strong>
                                                                                <small class="d-block">mais imagens</small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                <!-- Vídeos com Thumbnail -->
                                                @if(count($videosAgrupados) > 0)
                                                    <div class="media-section mb-4">
                                                        <div class="d-flex align-items-center mb-3">
                                                            <i class="fa fa-play-circle text-danger fa-lg mr-2"></i>
                                                            <span class="font-weight-bold text-dark">
                                                                Vídeos ({{ count($videosAgrupados) }})
                                                            </span>
                                                            <span class="badge badge-danger ml-2">HD</span>
                                                        </div>
                                                        <div class="row">
                                                            @foreach(array_slice($videosAgrupados, 0, 4) as $index => $videoData)
                                                                @php
                                                                    $isStreamingVideo = Str::contains($videoData['video'], 'm3u8');
                                                                    $extensao = pathinfo(parse_url($videoData['video'], PHP_URL_PATH), PATHINFO_EXTENSION);
                                                                    $isPlayableVideo = in_array(strtolower($extensao), ['mp4', 'webm']);
                                                                    $qualidadeTexto = is_numeric($videoData['qualidade']) ? $videoData['qualidade'].'p' : 'HD';
                                                                @endphp
                                                                
                                                                <div class="col-lg-3 col-md-4 col-6 mb-3">
                                                                    <div class="video-thumbnail-container position-relative" 
                                                                         id="video-container-{{ $post->id }}-{{ $index }}"
                                                                         onclick="playVideoInline('{{ $videoData['video'] }}', '{{ $videoData['thumbnail'] }}', '{{ $post->id }}-{{ $index }}', {{ $isPlayableVideo ? 'true' : 'false' }})">
                                                                        
                                                                        <!-- Thumbnail ou placeholder -->
                                                                        @if($videoData['thumbnail'])
                                                                            <img src="{{ $videoData['thumbnail'] }}" 
                                                                                 class="video-thumbnail img-fluid" 
                                                                                 alt="Preview do vídeo {{ $index + 1 }}" 
                                                                                 loading="lazy">
                                                                        @else
                                                                            <div class="video-placeholder d-flex align-items-center justify-content-center">
                                                                                <i class="fa fa-video-camera fa-2x text-white"></i>
                                                                            </div>
                                                                        @endif
                                                                        
                                                                        <!-- Overlay de play -->
                                                                        <div class="video-play-overlay">
                                                                            <div class="play-button">
                                                                                @if($isPlayableVideo)
                                                                                    <i class="fa fa-play-circle fa-3x text-white"></i>
                                                                                @elseif($isStreamingVideo)
                                                                                    <i class="fa fa-external-link-square fa-3x text-white"></i>
                                                                                @else
                                                                                    <i class="fa fa-download fa-2x text-white"></i>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        
                                                                        <!-- Badges informativos -->
                                                                        <div class="video-info-badges">
                                                                            <span class="badge badge-dark badge-quality">{{ $qualidadeTexto }}</span>
                                                                            @if($isStreamingVideo)
                                                                                <span class="badge badge-warning badge-type">Stream</span>
                                                                            @elseif($isPlayableVideo)
                                                                                <span class="badge badge-success badge-type">Play</span>
                                                                            @else
                                                                                <span class="badge badge-info badge-type">Download</span>
                                                                            @endif
                                                                        </div>
                                                                        
                                                                        <!-- Duração (simulada) -->
                                                                        <div class="video-duration">
                                                                            <span class="badge badge-dark">{{ rand(15, 180) }}s</span>
                                                                        </div>
                                                                        
                                                                        <!-- Indicador de hover -->
                                                                        <div class="video-hover-effect"></div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                            @if(count($videosAgrupados) > 4)
                                                                <div class="col-12">
                                                                    <div class="text-center p-3 bg-light rounded">
                                                                        <small class="text-muted">
                                                                            <i class="fa fa-plus-circle mr-1"></i>
                                                                            <strong>{{ count($videosAgrupados) - 4 }}</strong> vídeo(s) adicional(is)
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                <!-- Outros arquivos -->
                                                @if(count($outros) > 0)
                                                    <div class="media-section">
                                                        <small class="text-muted font-weight-bold d-block mb-2">
                                                            <i class="fa fa-file text-info"></i> Outros arquivos ({{ count($outros) }})
                                                        </small>
                                                        <div class="d-flex flex-wrap">
                                                            @foreach($outros as $arquivo)
                                                                <a href="{{ $arquivo }}" target="_blank" 
                                                                   class="btn btn-sm btn-outline-info mr-2 mb-2">
                                                                    <i class="fa fa-download"></i> Arquivo
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info text-center">
                            <i class="fa fa-info-circle fa-2x mb-2"></i><br>
                            <strong>Nenhum post encontrado</strong><br>
                            Não há posts coletados para os filtros selecionados.
                        </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Paginação -->
            <div class="row">
                <div class="col-md-12 text-center">
                    {{ $posts->onEachSide(1)->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}
                    <nav aria-label="Paginação">
                        <ul class="pagination justify-content-center">
                            <li class="page-item disabled"><span class="page-link">« Anterior</span></li>
                            <li class="page-item active"><span class="page-link">1</span></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item"><a class="page-link" href="#">Próximo »</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Detalhes do Post -->
<div class="modal fade" id="modalDetalhesPost" tabindex="-1" role="dialog" aria-labelledby="modalDetalhesPostLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetalhesPostLabel">
                    <i class="fa fa-info-circle"></i> Detalhes do Post
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
            $('#frm_filtro_posts').submit();
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
                case 'mesanterior':
                    dt_inicial = hoje.clone().subtract(1, 'months').startOf('month').format('YYYY-MM-DD');
                    dt_final = hoje.clone().subtract(1, 'months').endOf('month').format('YYYY-MM-DD');
                    break;
            }

            $('#data_inicial').val(dt_inicial);
            $('#data_final').val(dt_final);
        });

        // Detectar e aplicar orientação de vídeos automaticamente
        detectAndApplyVideoAspectRatios();
    });

    /**
     * Detecta a orientação dos vídeos baseado nas dimensões da URL e aplica as classes CSS apropriadas
     */
    function detectAndApplyVideoAspectRatios() {
        $('.video-thumbnail-container').each(function() {
            const container = $(this);
            const videoUrl = container.attr('onclick');
            
            if (!videoUrl) return;
            
            // Extrair URL do vídeo do atributo onclick
            const urlMatch = videoUrl.match(/'([^']+)'/);
            if (!urlMatch || !urlMatch[1]) return;
            
            const url = urlMatch[1];
            const aspectClass = detectVideoAspectRatio(url);
            
            // Remover classes de aspect ratio existentes
            container.removeClass('aspect-16-9 aspect-9-16 aspect-1-1 aspect-4-3 aspect-3-4 aspect-unknown');
            
            // Aplicar nova classe
            container.addClass(aspectClass);
            
            // Adicionar badge indicativo da orientação
            updateVideoOrientationBadge(container, aspectClass);
        });
    }

    /**
     * Detecta o aspect ratio do vídeo baseado na URL
     */
    function detectVideoAspectRatio(videoUrl) {
        // Padrões comuns de dimensões em URLs de vídeo
        const dimensionPatterns = [
            /(\d+)x(\d+)/g,           // 1920x1080, 720x1280, etc.
            /(\d+)_(\d+)/g,           // 1920_1080, 720_1280, etc.  
            /_(\d+)p_/g,              // _1080p_, _720p_, etc. (apenas altura)
            /res_(\d+)_(\d+)/g,       // res_1920_1080, etc.
        ];

        let width = null;
        let height = null;
        
        // Tentar extrair dimensões da URL
        for (const pattern of dimensionPatterns) {
            const matches = Array.from(videoUrl.matchAll(pattern));
            if (matches.length > 0) {
                const match = matches[matches.length - 1]; // Usar a última ocorrência
                
                if (pattern.source.includes('_(\d+)p_')) {
                    // Para padrões só com altura, assumir largura baseada em padrões comuns
                    height = parseInt(match[1]);
                    if (height <= 720) {
                        width = height < 640 ? Math.round(height * 9/16) : Math.round(height * 16/9);
                    } else {
                        width = Math.round(height * 16/9);
                    }
                } else {
                    width = parseInt(match[1]);
                    height = parseInt(match[2]);
                }
                break;
            }
        }

        // Se não encontrou dimensões na URL, tentar padrões específicos de plataforma
        if (!width || !height) {
            if (videoUrl.includes('stories') || videoUrl.includes('reel') || videoUrl.includes('short')) {
                return 'aspect-9-16'; // Stories/Reels são tipicamente verticais
            }
            
            if (videoUrl.includes('720x1280') || videoUrl.includes('1080x1920')) {
                return 'aspect-9-16'; // Vertical conhecido
            }
            
            if (videoUrl.includes('1920x1080') || videoUrl.includes('1280x720')) {
                return 'aspect-16-9'; // Horizontal conhecido
            }
            
            return 'aspect-16-9'; // Padrão horizontal
        }

        // Calcular aspect ratio baseado nas dimensões
        const aspectRatio = width / height;
        
        if (Math.abs(aspectRatio - 16/9) < 0.1) {
            return 'aspect-16-9'; // 1920x1080, 1280x720, etc.
        } else if (Math.abs(aspectRatio - 9/16) < 0.1) {
            return 'aspect-9-16'; // 720x1280, 1080x1920, etc.
        } else if (Math.abs(aspectRatio - 1) < 0.1) {
            return 'aspect-1-1'; // 1080x1080, 720x720, etc.
        } else if (Math.abs(aspectRatio - 4/3) < 0.1) {
            return 'aspect-4-3'; // 1024x768, 800x600, etc.
        } else if (Math.abs(aspectRatio - 3/4) < 0.1) {
            return 'aspect-3-4'; // 768x1024, 600x800, etc.
        } else if (aspectRatio < 1) {
            return 'aspect-9-16'; // Qualquer formato vertical
        } else {
            return 'aspect-16-9'; // Qualquer formato horizontal
        }
    }

    /**
     * Atualiza o badge de orientação do vídeo
     */
    function updateVideoOrientationBadge(container, aspectClass) {
        let orientationText = '';
        let badgeClass = 'badge-info';
        
        switch(aspectClass) {
            case 'aspect-9-16':
                orientationText = 'Vertical';
                badgeClass = 'badge-warning';
                break;
            case 'aspect-16-9':
                orientationText = 'Horizontal';
                badgeClass = 'badge-primary';
                break;
            case 'aspect-1-1':
                orientationText = 'Quadrado';
                badgeClass = 'badge-success';
                break;
            case 'aspect-4-3':
                orientationText = '4:3';
                badgeClass = 'badge-secondary';
                break;
            case 'aspect-3-4':
                orientationText = '3:4';
                badgeClass = 'badge-dark';
                break;
            default:
                orientationText = 'Auto';
                badgeClass = 'badge-light';
        }
        
        // Encontrar badge de tipo existente e adicionar orientação
        const typeBadge = container.find('.badge-type');
        if (typeBadge.length > 0) {
            // Adicionar orientação ao badge existente
            const currentText = typeBadge.text();
            typeBadge.html(`${currentText} • ${orientationText}`);
            
            // Adicionar classe de orientação se não for o padrão
            if (aspectClass !== 'aspect-16-9') {
                typeBadge.addClass(badgeClass.replace('badge-', 'text-'));
            }
        }
    }
    
    function verDetalhes(postId) {
        var host = $('meta[name="base-url"]').attr('content');
        
        $('#modalDetalhesPost').modal('show');
        $('#conteudoDetalhesPost').html(`
            <div class="text-center p-4">
                <i class="fa fa-spinner fa-spin fa-2x text-primary"></i>
                <p class="mt-2">Carregando detalhes do post...</p>
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
    
    function exportarPosts() {
        var host = $('meta[name="base-url"]').attr('content');
        var filtros = $('#frm_filtro_posts').serialize();
        
        Swal.fire({
            title: 'Exportar Posts',
            text: 'Escolha o formato para exportação:',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="fa fa-file-excel-o"></i> Excel',
            cancelButtonText: '<i class="fa fa-file-text-o"></i> CSV',
            showDenyButton: true,
            denyButtonText: '<i class="fa fa-file-pdf-o"></i> PDF'
        }).then((result) => {
            var formato = '';
            if (result.isConfirmed) {
                formato = 'excel';
            } else if (result.isDenied) {
                formato = 'pdf';
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                formato = 'csv';
            }
            
            if (formato) {
                window.open(host + '/midias-sociais/posts/exportar?formato=' + formato + '&' + filtros, '_blank');
            }
        });
    }
    
    function abrirImagem(url) {
        // Verificar se SweetAlert está disponível
        if (typeof Swal !== 'undefined') {
            // Mostrar loading primeiro
            Swal.fire({
                title: 'Carregando imagem...',
                html: '<i class="fa fa-spinner fa-spin fa-2x"></i>',
                showConfirmButton: false,
                allowOutsideClick: false,
                background: '#000',
                color: '#fff'
            });

            // Criar uma imagem temporária para verificar se carrega
            const tempImg = new Image();
            tempImg.onload = function() {
                // Imagem carregou com sucesso, mostrar no modal
                Swal.fire({
                    imageUrl: url,
                    imageAlt: 'Imagem ampliada',
                    showConfirmButton: false,
                    showCloseButton: true,
                    showCancelButton: true,
                    cancelButtonText: '<i class="fa fa-external-link"></i> Nova Aba',
                    width: '95vw',
                    padding: '0',
                    imageWidth: '100%',
                    imageHeight: 'auto',
                    backdrop: `rgba(0,0,0,0.95)`,
                    background: '#000',
                    customClass: {
                        popup: 'swal-image-modal-enhanced',
                        image: 'swal-full-image-enhanced',
                        cancelButton: 'btn-outline-primary'
                    },
                    didOpen: () => {
                        const image = Swal.getPopup().querySelector('.swal2-image');
                        if (image) {
                            // Configurar imagem responsiva
                            image.style.maxHeight = '90vh';
                            image.style.maxWidth = '95vw';
                            image.style.objectFit = 'contain';
                            image.style.cursor = 'zoom-in';
                            image.style.borderRadius = '8px';
                            image.style.boxShadow = '0 10px 30px rgba(0,0,0,0.5)';
                            image.title = 'Clique para aumentar/diminuir';
                            
                            let isZoomed = false;
                            image.onclick = function(e) {
                                e.stopPropagation();
                                if (!isZoomed) {
                                    image.style.transform = 'scale(1.5)';
                                    image.style.cursor = 'zoom-out';
                                    image.style.transition = 'transform 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                                    isZoomed = true;
                                } else {
                                    image.style.transform = 'scale(1)';
                                    image.style.cursor = 'zoom-in';
                                    isZoomed = false;
                                }
                            };

                            // Adicionar suporte para scroll wheel zoom
                            image.addEventListener('wheel', function(e) {
                                e.preventDefault();
                                const delta = e.deltaY > 0 ? -0.1 : 0.1;
                                const currentScale = image.style.transform ? 
                                    parseFloat(image.style.transform.match(/scale\(([^)]+)\)/)?.[1] || 1) : 1;
                                const newScale = Math.max(0.5, Math.min(3, currentScale + delta));
                                
                                image.style.transform = `scale(${newScale})`;
                                image.style.transition = 'transform 0.1s ease';
                                image.style.cursor = newScale > 1 ? 'zoom-out' : 'zoom-in';
                            });

                            // Adicionar controles de teclado
                            const keyHandler = function(e) {
                                switch(e.key) {
                                    case 'Escape':
                                        Swal.close();
                                        break;
                                    case '+':
                                    case '=':
                                        image.click();
                                        break;
                                    case '-':
                                        if (isZoomed) image.click();
                                        break;
                                }
                            };
                            document.addEventListener('keydown', keyHandler);
                            
                            // Remover listener quando modal fechar
                            Swal.getPopup().addEventListener('swal2-close', () => {
                                document.removeEventListener('keydown', keyHandler);
                            });
                        }
                    }
                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.cancel) {
                        window.open(url, '_blank');
                    }
                });
            };

            tempImg.onerror = function() {
                // Erro ao carregar imagem
                Swal.fire({
                    title: 'Erro ao carregar imagem',
                    text: 'A imagem não pôde ser carregada. Verifique sua conexão.',
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fa fa-external-link"></i> Tentar em Nova Aba',
                    cancelButtonText: 'Fechar',
                    background: '#000',
                    color: '#fff'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.open(url, '_blank');
                    }
                });
            };

            // Iniciar carregamento da imagem
            tempImg.src = url;
        } else {
            // Fallback: Criar modal simples se SweetAlert não estiver disponível
            createImageModal(url);
        }
    }
    
    function createImageModal(url) {
        // Fechar modal existente se houver
        closeImageModal();
        
        // Criar modal customizado para imagens
        const modal = document.createElement('div');
        modal.className = 'custom-image-modal';
        modal.innerHTML = `
            <div class="custom-modal-backdrop" onclick="closeImageModal()">
                <div class="custom-modal-content" onclick="event.stopPropagation()">
                    <div class="custom-modal-header">
                        <button class="custom-close-btn" onclick="closeImageModal()" title="Fechar">&times;</button>
                        <button class="custom-zoom-btn" onclick="toggleImageZoom()" title="Zoom">🔍</button>
                    </div>
                    <div class="custom-modal-body">
                        <div class="image-loading">
                            <i class="fa fa-spinner fa-spin fa-2x text-white"></i>
                            <p class="text-white mt-2">Carregando imagem...</p>
                        </div>
                        <img src="${url}" alt="Imagem ampliada" class="custom-modal-image" 
                             onload="hideImageLoading()" 
                             onerror="showImageError(this, '${url}')"
                             style="display: none;">
                    </div>
                    <div class="custom-modal-footer">
                        <div class="modal-actions">
                            <button onclick="toggleImageZoom()" class="btn btn-sm btn-outline-light mr-2">
                                <i class="fa fa-search-plus"></i> Zoom
                            </button>
                            <a href="${url}" target="_blank" class="btn btn-sm btn-primary">
                                <i class="fa fa-external-link"></i> Nova Aba
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        modal.style.display = 'flex';
        
        // Adicionar listener para ESC
        const escListener = function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
                document.removeEventListener('keydown', escListener);
            }
        };
        document.addEventListener('keydown', escListener);
    }
    
    function closeImageModal() {
        const modal = document.querySelector('.custom-image-modal');
        if (modal) {
            modal.style.opacity = '0';
            setTimeout(() => modal.remove(), 200);
        }
    }
    
    function hideImageLoading() {
        const loading = document.querySelector('.image-loading');
        const image = document.querySelector('.custom-modal-image');
        if (loading && image) {
            loading.style.display = 'none';
            image.style.display = 'block';
            // Animação de entrada da imagem
            image.style.opacity = '0';
            image.style.transform = 'scale(0.8)';
            setTimeout(() => {
                image.style.transition = 'all 0.3s ease';
                image.style.opacity = '1';
                image.style.transform = 'scale(1)';
            }, 50);
        }
    }
    
    function showImageError(img, url) {
        const loading = document.querySelector('.image-loading');
        const modalBody = img.parentElement;
        
        if (loading) {
            loading.style.display = 'none';
        }
        
        modalBody.innerHTML = `
            <div class="image-error text-center text-white py-4">
                <i class="fa fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                <h5>Erro ao carregar imagem</h5>
                <p class="text-muted">A imagem não pôde ser carregada. Verifique sua conexão ou tente novamente.</p>
                <div class="mt-3">
                    <button onclick="retryImageLoad('${url}')" class="btn btn-outline-warning mr-2">
                        <i class="fa fa-refresh"></i> Tentar Novamente
                    </button>
                    <a href="${url}" target="_blank" class="btn btn-primary">
                        <i class="fa fa-external-link"></i> Abrir em Nova Aba
                    </a>
                </div>
            </div>
        `;
    }
    
    function retryImageLoad(url) {
        const modalBody = document.querySelector('.custom-modal-body');
        if (modalBody) {
            modalBody.innerHTML = `
                <div class="image-loading">
                    <i class="fa fa-spinner fa-spin fa-2x text-white"></i>
                    <p class="text-white mt-2">Tentando novamente...</p>
                </div>
                <img src="${url}?t=${Date.now()}" alt="Imagem ampliada" class="custom-modal-image" 
                     onload="hideImageLoading()" 
                     onerror="showImageError(this, '${url}')"
                     style="display: none;">
            `;
        }
    }
    
    function toggleImageZoom() {
        const image = document.querySelector('.custom-modal-image');
        const zoomBtn = document.querySelector('.custom-zoom-btn');
        
        if (image && zoomBtn) {
            if (image.style.transform && image.style.transform.includes('scale(1.5)')) {
                // Zoom out
                image.style.transform = 'scale(1)';
                image.style.cursor = 'zoom-in';
                zoomBtn.innerHTML = '🔍';
                zoomBtn.title = 'Ampliar';
            } else {
                // Zoom in
                image.style.transform = 'scale(1.5)';
                image.style.cursor = 'zoom-out';
                zoomBtn.innerHTML = '🔍-';
                zoomBtn.title = 'Reduzir';
                image.style.transition = 'transform 0.3s ease';
            }
        }
    }
    
    function playVideoInline(videoUrl, thumbnailUrl, containerId, isPlayableVideo) {
        const container = document.getElementById('video-container-' + containerId);
        
        if (!container) {
            console.error('Container não encontrado:', containerId);
            return;
        }
        
        if (!isPlayableVideo) {
            // Para vídeos não reproduzíveis, abrir em nova aba
            window.open(videoUrl, '_blank');
            return;
        }
        
        // Verificar se já está reproduzindo
        const existingVideo = container.querySelector('video');
        if (existingVideo) {
            // Se já tem vídeo, pausar/reproduzir
            if (existingVideo.paused) {
                existingVideo.play();
            } else {
                existingVideo.pause();
            }
            return;
        }
        
        // Criar o elemento de vídeo
        const videoElement = document.createElement('video');
        videoElement.src = videoUrl;
        videoElement.controls = true;
        videoElement.autoplay = true;
        videoElement.className = 'inline-video-player';
        videoElement.style.cssText = `
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
            background: #000;
        `;
        
        // Adicionar poster se disponível
        if (thumbnailUrl) {
            videoElement.poster = thumbnailUrl;
        }
        
        // Adicionar botão de fechar
        const closeButton = document.createElement('button');
        closeButton.innerHTML = '&times;';
        closeButton.className = 'inline-video-close';
        closeButton.style.cssText = `
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(0,0,0,0.7);
            color: white;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            font-size: 18px;
            cursor: pointer;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s ease;
        `;
        
        closeButton.onmouseover = function() {
            this.style.background = 'rgba(255,0,0,0.7)';
        };
        
        closeButton.onmouseout = function() {
            this.style.background = 'rgba(0,0,0,0.7)';
        };
        
        closeButton.onclick = function(e) {
            e.stopPropagation();
            restoreVideoThumbnail(container, thumbnailUrl);
        };
        
        // Adicionar controles de qualidade se possível
        const qualityBadge = document.createElement('div');
        qualityBadge.className = 'inline-video-quality';
        qualityBadge.innerHTML = '<span class="badge badge-success">▶ Reproduzindo</span>';
        qualityBadge.style.cssText = `
            position: absolute;
            top: 8px;
            left: 8px;
            z-index: 10;
        `;
        
        // Limpar conteúdo atual e adicionar vídeo
        container.innerHTML = '';
        container.appendChild(videoElement);
        container.appendChild(closeButton);
        container.appendChild(qualityBadge);
        
        // Adicionar classe para indicar que está reproduzindo
        container.classList.add('playing-video');
        
        // Event listeners do vídeo
        videoElement.addEventListener('loadstart', function() {
            console.log('Iniciando carregamento do vídeo...');
            qualityBadge.innerHTML = '<span class="badge badge-warning">⏳ Carregando...</span>';
        });
        
        videoElement.addEventListener('canplay', function() {
            qualityBadge.innerHTML = '<span class="badge badge-success">▶ Pronto</span>';
            setTimeout(() => {
                qualityBadge.style.opacity = '0';
            }, 2000);
        });
        
        videoElement.addEventListener('error', function(e) {
            console.error('Erro ao carregar vídeo:', e);
            qualityBadge.innerHTML = '<span class="badge badge-danger">❌ Erro</span>';
            
            // Fallback: tentar abrir em nova aba
            setTimeout(() => {
                if (confirm('Erro ao reproduzir o vídeo. Deseja abrir em nova aba?')) {
                    window.open(videoUrl, '_blank');
                }
                restoreVideoThumbnail(container, thumbnailUrl);
            }, 3000);
        });
        
        videoElement.addEventListener('ended', function() {
            qualityBadge.innerHTML = '<span class="badge badge-info">🔄 Finalizado - Clique para repetir</span>';
            qualityBadge.style.opacity = '1';
            
            // Opção para repetir
            setTimeout(() => {
                container.onclick = function() {
                    videoElement.currentTime = 0;
                    videoElement.play();
                    qualityBadge.style.opacity = '0';
                };
            }, 1000);
        });
    }
    
    function restoreVideoThumbnail(container, thumbnailUrl) {
        // Restaurar o thumbnail original
        const originalOnclick = container.getAttribute('onclick');
        
        container.innerHTML = `
            ${thumbnailUrl ? `<img src="${thumbnailUrl}" class="video-thumbnail img-fluid" alt="Preview do vídeo" loading="lazy">` : '<div class="video-placeholder d-flex align-items-center justify-content-center"><i class="fa fa-video-camera fa-2x text-white"></i></div>'}
            <div class="video-play-overlay">
                <div class="play-button">
                    <i class="fa fa-play-circle fa-3x text-white"></i>
                </div>
            </div>
            <div class="video-info-badges">
                <span class="badge badge-dark badge-quality">HD</span>
                <span class="badge badge-success badge-type">Play</span>
            </div>
            <div class="video-duration">
                <span class="badge badge-dark">▶ Clique para reproduzir</span>
            </div>
            <div class="video-hover-effect"></div>
        `;
        
        container.classList.remove('playing-video');
        
        // Restaurar funcionalidade original
        container.setAttribute('onclick', originalOnclick);
    }
    
    function abrirVideo(url, thumbnail = null) {
        // Verificar tipo de vídeo e abrir adequadamente
        const extensao = url.split('.').pop().toLowerCase().split('?')[0]; // Remove parâmetros da URL
        const isStreamingVideo = url.includes('m3u8') || extensao === 'm3u8';
        const isPlayableVideo = ['mp4', 'webm', 'ogg'].includes(extensao);
        const isOtherVideo = ['avi', 'mov', 'wmv', 'flv', 'mkv'].includes(extensao);
        
        if (isStreamingVideo) {
            // Para vídeos streaming (m3u8), abrir em nova aba
            Swal.fire({
                title: 'Vídeo de Streaming',
                text: 'Este vídeo será aberto em uma nova aba para reprodução.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Abrir Vídeo',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open(url, '_blank');
                }
            });
        } else if (isPlayableVideo) {
            // Para vídeos reproduzíveis, criar player modal
            let videoHtml = `
                <div class="video-player-container">
                    <video controls autoplay style="width: 100%; max-width: 800px; height: auto; border-radius: 8px;"`;
            
            // Se tem thumbnail, usar como poster
            if (thumbnail) {
                videoHtml += ` poster="${thumbnail}"`;
            }
            
            videoHtml += `>
                        <source src="${url}" type="video/${extensao}">
                        <p>Seu navegador não suporta reprodução de vídeo HTML5.</p>
                        <a href="${url}" target="_blank" class="btn btn-primary">
                            <i class="fa fa-external-link"></i> Abrir em Nova Aba
                        </a>
                    </video>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fa fa-info-circle"></i> Use os controles para navegar pelo vídeo
                            </small>
                            <div class="video-actions">
                                <button class="btn btn-sm btn-outline-primary" onclick="toggleFullscreen()">
                                    <i class="fa fa-expand"></i> Tela Cheia
                                </button>
                                <a href="${url}" download class="btn btn-sm btn-outline-success ml-2">
                                    <i class="fa fa-download"></i> Download
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            Swal.fire({
                title: `<i class="fa fa-play-circle text-success"></i> Reproduzindo Vídeo HD`,
                html: videoHtml,
                showConfirmButton: true,
                showCancelButton: true,
                confirmButtonText: '<i class="fa fa-external-link"></i> Abrir em Nova Aba',
                cancelButtonText: '<i class="fa fa-times"></i> Fechar',
                width: '90%',
                customClass: {
                    popup: 'swal-video-player',
                    confirmButton: 'btn-outline-primary',
                    cancelButton: 'btn-secondary'
                },
                didOpen: () => {
                    // Configurar eventos do vídeo
                    const video = Swal.getPopup().querySelector('video');
                    if (video) {
                        video.addEventListener('error', function() {
                            console.log('Erro ao carregar vídeo, tentando abrir em nova aba');
                            window.open(url, '_blank');
                        });
                        
                        video.addEventListener('loadstart', function() {
                            console.log('Iniciando carregamento do vídeo...');
                        });
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open(url, '_blank');
                }
            });
        } else if (isOtherVideo) {
            // Para outros formatos de vídeo, dar opções ao usuário
            Swal.fire({
                title: 'Formato de Vídeo',
                html: `
                    <div class="text-center">
                        <i class="fa fa-file-video-o fa-3x text-warning mb-3"></i>
                        <p>Este vídeo está em formato <strong>${extensao.toUpperCase()}</strong>.</p>
                        <p class="text-muted">Escolha como deseja abrir:</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: '<i class="fa fa-download"></i> Download',
                denyButtonText: '<i class="fa fa-external-link"></i> Nova Aba',
                cancelButtonText: '<i class="fa fa-times"></i> Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Forçar download
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = `video.${extensao}`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                } else if (result.isDenied) {
                    // Abrir em nova aba
                    window.open(url, '_blank');
                }
            });
        } else {
            // Para tipos desconhecidos, tentar abrir em nova aba
            window.open(url, '_blank');
        }
    }
    
    function filtrarHashtag(hashtag) {
        // Adicionar hashtag ao campo de palavra-chave e submeter formulário
        let palavraChaveAtual = $('#palavra_chave').val();
        let novaHashtag = '#' + hashtag;
        
        // Se já não tiver a hashtag, adicionar
        if (!palavraChaveAtual.includes(novaHashtag)) {
            let novaPalavraChave = palavraChaveAtual ? palavraChaveAtual + ' ' + novaHashtag : novaHashtag;
            $('#palavra_chave').val(novaPalavraChave);
            $('#frm_filtro_posts').submit();
        }
    }
    
    function verTodasImagens(postId) {
        var host = $('meta[name="base-url"]').attr('content');
        
        // Usar modal para mostrar todas as imagens
        Swal.fire({
            title: 'Carregando imagens...',
            text: 'Por favor, aguarde',
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
                
                // Fazer requisição para buscar todas as imagens do post
                $.ajax({
                    url: host + '/midias-sociais/posts/' + postId + '/imagens',
                    type: 'GET',
                    success: function(response) {
                        if (response.imagens && response.imagens.length > 0) {
                            let imagensHtml = '<div class="row">';
                            response.imagens.forEach((imagem, index) => {
                                imagensHtml += `
                                    <div class="col-md-4 mb-3">
                                        <img src="${imagem}" 
                                             class="img-fluid rounded shadow" 
                                             alt="Imagem ${index + 1}"
                                             style="cursor: pointer; width: 100%; height: 200px; object-fit: cover;"
                                             onclick="abrirImagem('${imagem}')">
                                    </div>
                                `;
                            });
                            imagensHtml += '</div>';
                            
                            Swal.fire({
                                title: `Todas as imagens (${response.imagens.length})`,
                                html: imagensHtml,
                                showConfirmButton: true,
                                confirmButtonText: 'Fechar',
                                width: '80%',
                                customClass: {
                                    popup: 'swal-wide'
                                }
                            });
                        } else {
                            Swal.fire('Aviso', 'Nenhuma imagem encontrada', 'info');
                        }
                    },
                    error: function() {
                        Swal.fire('Erro', 'Não foi possível carregar as imagens', 'error');
                    }
                });
            }
        });
    }
    
    function toggleFullscreen() {
        const video = document.querySelector('#swal2-content video');
        if (video) {
            if (video.requestFullscreen) {
                video.requestFullscreen();
            } else if (video.webkitRequestFullscreen) {
                video.webkitRequestFullscreen();
            } else if (video.mozRequestFullScreen) {
                video.mozRequestFullScreen();
            }
        }
    }
    
    // === FUNCIONALIDADE DE SELEÇÃO MÚLTIPLA DE POSTS ===
    
    function updateSelectionUI() {
        const checkboxes = document.querySelectorAll('.post-checkbox:checked');
        const count = checkboxes.length;
        
        // Atualizar contador
        document.getElementById('selected-count').textContent = count;
        
        // Mostrar/esconder elementos baseado na seleção
        const selectionInfo = document.getElementById('selection-info');
        const btnCriarNoticias = document.getElementById('btn-criar-noticias');
        
        if (count > 0) {
            selectionInfo.style.display = 'block';
            btnCriarNoticias.style.display = 'inline-block';
        } else {
            selectionInfo.style.display = 'none';
            btnCriarNoticias.style.display = 'none';
        }
        
        // Atualizar checkbox "selecionar todos"
        const selectAllCheckbox = document.getElementById('select-all');
        const allCheckboxes = document.querySelectorAll('.post-checkbox');
        
        if (count === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (count === allCheckboxes.length) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
    }
    
    function criarNoticiasDosPosts() {
        const checkboxes = document.querySelectorAll('.post-checkbox:checked');
        const postsIds = Array.from(checkboxes).map(cb => cb.value);
        
        if (postsIds.length === 0) {
            Swal.fire({
                title: 'Nenhum post selecionado',
                text: 'Por favor, selecione pelo menos um post para criar notícias.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }
        
        // Verificar se há posts já processados
        const processedPosts = Array.from(checkboxes).filter(cb => cb.dataset.processed === '1');
        let confirmText = `Criar notícias a partir dos ${postsIds.length} posts selecionados?`;
        
        if (processedPosts.length > 0) {
            confirmText += `\n\nAviso: ${processedPosts.length} post(s) já foram processados anteriormente. Estes podem já ter notícias criadas.`;
        }
        
        Swal.fire({
            title: 'Confirmar criação de notícias',
            text: confirmText,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, criar notícias!',
            cancelButtonText: 'Cancelar',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                const host = $('meta[name="base-url"]').attr('content');
                const token = $('meta[name="csrf-token"]').attr('content');
                
                return $.ajax({
                    url: host + '/midias-sociais/posts/criar-noticias',
                    type: 'POST',
                    data: {
                        posts_ids: postsIds,
                        _token: token
                    },
                    dataType: 'json'
                })
                .then(response => {
                    return response;
                })
                .catch(xhr => {
                    console.error('Erro completo:', xhr);
                    let errorMessage = 'Erro na requisição';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        // Tentar extrair mensagem de erro do HTML
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = xhr.responseText;
                        const errorElement = tempDiv.querySelector('.alert-danger, .error');
                        if (errorElement) {
                            errorMessage = errorElement.textContent.trim();
                        }
                    }
                    
                    throw new Error(errorMessage);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value && result.value.redirect) {
                    window.location.href = result.value.redirect;
                } else {
                    // Mostrar mensagem de sucesso e recarregar
                    Swal.fire({
                        title: 'Sucesso!',
                        text: result.value && result.value.message ? result.value.message : 'Notícias criadas com sucesso!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.reload();
                    });
                }
            }
        });
    }
    
    // Inicializar funcionalidade de seleção quando a página carregar
    $(document).ready(function() {
        // Selecionar/deselecionar todos
        $('#select-all').on('change', function() {
            const isChecked = this.checked;
            $('.post-checkbox').prop('checked', isChecked);
            updateSelectionUI();
        });
        
        // Atualizar UI quando checkbox individual muda
        $(document).on('change', '.post-checkbox', function() {
            updateSelectionUI();
            
            // Destacar visualmente post selecionado
            const postCard = $(this).closest('.post-card');
            if (this.checked) {
                postCard.addClass('selected-post');
            } else {
                postCard.removeClass('selected-post');
            }
        });
        
        // Inicializar estado da UI
        updateSelectionUI();
    });
</script>

<style>
    /* Estilos para melhorar a apresentação dos posts */
    .post-media {
        transition: all 0.3s ease;
    }
    
    /* === ESTILOS PARA SELEÇÃO MÚLTIPLA === */
    .post-card {
        transition: all 0.3s ease;
    }
    
    .post-card.selected-post {
        border: 2px solid #28a745;
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.2);
        background-color: rgba(40, 167, 69, 0.02);
    }
    
    .selected-post .card-body {
        border-left: 4px solid #28a745;
    }
    
    /* === LAYOUT DO CHECKBOX E ÍCONE === */
    .post-selection-checkbox {
        position: absolute;
        top: -5px;
        right: -5px;
        z-index: 10;
        background: white;
        border-radius: 50%;
        padding: 2px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        border: 2px solid #f8f9fa;
        transition: all 0.3s ease;
    }
    
    .post-selection-checkbox:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        border-color: #28a745;
    }
    
    .post-selection-checkbox .form-check-label {
        margin: 0;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
    }
    
    .post-selection-checkbox .form-check-input {
        position: relative;
        margin: 0;
        width: 16px;
        height: 16px;
    }
    
    .post-selection-checkbox .form-check-sign {
        width: 16px;
        height: 16px;
    }
    
    .social-icon-container {
        margin-top: 15px;
    }
    
    .processed-indicator {
        margin-top: 10px;
    }
    
    .processed-indicator small {
        font-size: 0.7rem;
        padding: 2px 6px;
        background: rgba(40, 167, 69, 0.1);
        border-radius: 10px;
        display: inline-block;
    }
    
    .news-indicator small {
        font-size: 0.7rem;
        padding: 2px 6px;
        background: rgba(0, 123, 255, 0.1);
        border-radius: 10px;
        display: inline-block;
        border: 1px solid rgba(0, 123, 255, 0.2);
    }
    
    /* Quando o post está selecionado, destacar o checkbox */
    .selected-post .post-selection-checkbox {
        background: #28a745;
        border-color: #28a745;
    }
    
    .selected-post .post-selection-checkbox .form-check-sign::before {
        border-color: white !important;
        background: white !important;
    }
    
    .selected-post .post-selection-checkbox .form-check-input:checked + .form-check-sign::before {
        background-color: white !important;
        border-color: white !important;
    }
    
    .btn-orange {
        background-color: #fd7e14;
        border-color: #fd7e14;
        color: white;
        transition: all 0.3s ease;
    }
    
    .btn-orange:hover {
        background-color: #e8650e;
        border-color: #e8650e;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(253, 126, 20, 0.3);
    }
    
    .btn-orange:active {
        background-color: #d15509;
        border-color: #d15509;
    }
    
    .form-check-input:checked + .form-check-sign::before {
        background-color: #28a745;
        border-color: #28a745;
    }
    
    #select-all:indeterminate + .form-check-sign::before {
        background-color: #ffc107;
        border-color: #ffc107;
    }
    
    /* Animação para contador de selecionados */
    #selection-info {
        animation: slideIn 0.3s ease;
    }
    
    @keyframes slideIn {
        from { opacity: 0; transform: translateX(-10px); }
        to { opacity: 1; transform: translateX(0); }
    }
    
    /* Hover effect para posts */
    .post-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .post-card.selected-post:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(40, 167, 69, 0.3);
    }
    
    .image-container:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .video-container {
        position: relative;
        overflow: hidden;
        border-radius: 10px !important;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    
    .video-container:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.25);
    }
    
    .video-container .video-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.1);
        transition: background 0.3s ease;
        pointer-events: none;
    }
    
    .video-container:hover .video-overlay {
        background: rgba(0,0,0,0.2);
    }
    
    .video-container i.fa {
        transition: transform 0.3s ease;
        text-shadow: 0 2px 4px rgba(0,0,0,0.5);
    }
    
    .video-container:hover i.fa {
        transform: scale(1.1);
    }
    
    .badge:hover {
        transform: scale(1.05);
        transition: transform 0.1s ease;
    }
    
    .post-tags .badge {
        font-size: 0.8rem;
        margin-bottom: 4px;
        transition: all 0.2s ease;
    }
    
    .post-tags .badge:hover {
        text-decoration: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    
    .social-icon i {
        transition: transform 0.2s ease;
    }
    
    .social-icon:hover i {
        transform: scale(1.1);
    }
    
    .card {
        transition: box-shadow 0.3s ease;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .media-section {
        border-left: 3px solid #007bff;
        padding-left: 10px;
        margin-bottom: 15px;
    }
    
    .image-overlay {
        background: linear-gradient(45deg, rgba(0,0,0,0.8), rgba(0,0,0,0.6)) !important;
    }
    
    .relevancia-score .badge {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
    }
    
    /* Responsividade para mobile */
    @media (max-width: 768px) {
        .post-media .col-4 {
            padding-left: 2px;
            padding-right: 2px;
        }
        
        .post-media img {
            height: 80px !important;
        }
        
        .post-tags .badge {
            font-size: 0.7rem;
            margin-bottom: 2px;
        }
        
        /* Checkboxes maiores para mobile */
        .post-selection-checkbox {
            top: 5px;
            right: 5px;
            padding: 4px;
            transform: scale(1.2);
        }
        
        .post-selection-checkbox .form-check-label {
            width: 24px;
            height: 24px;
        }
        
        .post-selection-checkbox .form-check-input {
            width: 20px;
            height: 20px;
        }
        
        .post-selection-checkbox .form-check-sign {
            width: 20px;
            height: 20px;
        }
        
        .social-icon-container {
            margin-top: 25px;
        }
        
        .social-icon-container .social-icon i {
            font-size: 1.5rem !important;
        }
        
        .processed-indicator small {
            font-size: 0.6rem;
        }
    }
    
    /* SweetAlert custom styles */
    .swal-wide {
        width: 90% !important;
    }
    
    /* Player de vídeo no modal */
    .swal-video-player {
        border-radius: 15px !important;
    }
    
    .swal-video-player video {
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        background: #000;
    }
    
    .swal-video-player .swal2-html-container {
        margin: 0 !important;
        padding: 20px !important;
    }
    
    .video-player-container {
        text-align: center;
    }
    
    .video-player-container video {
        max-height: 500px;
        width: 100%;
        object-fit: contain;
    }
    
    .swal-video-player .swal2-title {
        font-size: 1.2rem;
        margin-bottom: 15px;
    }
    
    .swal-video-player .swal2-actions {
        justify-content: space-between;
        gap: 10px;
    }
    
    .swal-video-player .swal2-confirm, 
    .swal-video-player .swal2-cancel,
    .swal-video-player .swal2-deny {
        font-size: 0.9rem;
        padding: 8px 16px;
        border-radius: 6px;
    }
    
    /* === ESTILOS PARA VÍDEOS COM THUMBNAIL === */
    
    .video-thumbnail-container {
        cursor: pointer;
        border-radius: 12px;
        overflow: hidden;
        background: #000;
        position: relative;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        min-height: 200px;
    }

    /* Aspect ratios dinâmicos para vídeos */
    .video-thumbnail-container.aspect-16-9 {
        aspect-ratio: 16/9;
    }

    .video-thumbnail-container.aspect-9-16 {
        aspect-ratio: 9/16;
        max-height: 400px;
    }

    .video-thumbnail-container.aspect-1-1 {
        aspect-ratio: 1/1;
    }

    .video-thumbnail-container.aspect-4-3 {
        aspect-ratio: 4/3;
    }

    .video-thumbnail-container.aspect-3-4 {
        aspect-ratio: 3/4;
        max-height: 350px;
    }

    /* Para vídeos sem dimensões conhecidas - usar padrão quadrado */
    .video-thumbnail-container.aspect-unknown {
        aspect-ratio: 16/9;
    }
    
    .video-thumbnail-container:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.25);
    }
    
    .video-thumbnail,
    .video-placeholder {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .video-placeholder {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 0;
    }
    
    .video-thumbnail-container:hover .video-thumbnail {
        transform: scale(1.05);
    }
    
    .video-play-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
        backdrop-filter: blur(2px);
    }
    
    .video-thumbnail-container:hover .video-play-overlay {
        opacity: 1;
    }
    
    .play-button {
        transition: transform 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        text-shadow: 0 2px 8px rgba(0,0,0,0.8);
    }
    
    .video-thumbnail-container:hover .play-button {
        transform: scale(1.2);
    }
    
    .video-info-badges {
        position: absolute;
        top: 8px;
        left: 8px;
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
    }
    
    .badge-quality {
        font-size: 0.7rem;
        padding: 2px 6px;
        background: rgba(0,0,0,0.8) !important;
        border: 1px solid rgba(255,255,255,0.2);
    }
    
    .badge-type {
        font-size: 0.6rem;
        padding: 2px 5px;
        font-weight: 600;
    }
    
    .video-duration {
        position: absolute;
        bottom: 8px;
        right: 8px;
    }
    
    .video-duration .badge {
        font-size: 0.7rem;
        padding: 2px 6px;
        background: rgba(0,0,0,0.8) !important;
        border: 1px solid rgba(255,255,255,0.2);
    }
    
    .video-hover-effect {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
        transform: translateX(-100%);
        transition: transform 0.6s ease;
    }
    
    .video-thumbnail-container:hover .video-hover-effect {
        transform: translateX(100%);
    }
    
    .video-actions .btn {
        border-radius: 20px;
        font-size: 0.8rem;
        padding: 4px 12px;
    }
    
    /* === RESPONSIVIDADE === */
    @media (max-width: 768px) {
        /* Ajustar aspect ratios para mobile */
        .video-thumbnail-container.aspect-16-9 {
            aspect-ratio: 16/9;
            max-height: 200px;
        }
        
        .video-thumbnail-container.aspect-9-16 {
            aspect-ratio: 9/16;
            max-height: 300px; /* Menor em mobile para vídeos verticais */
        }
        
        .video-thumbnail-container.aspect-1-1 {
            aspect-ratio: 1/1;
            max-height: 250px;
        }
        
        .video-thumbnail-container.aspect-4-3 {
            aspect-ratio: 4/3;
            max-height: 200px;
        }
        
        .video-thumbnail-container.aspect-3-4 {
            aspect-ratio: 3/4;
            max-height: 280px;
        }
        
        .video-thumbnail-container.aspect-unknown {
            aspect-ratio: 16/9;
            max-height: 200px;
        }
        
        .video-info-badges {
            top: 4px;
            left: 4px;
        }
        
        .video-duration {
            bottom: 4px;
            right: 4px;
        }
        
        .badge-quality,
        .badge-type,
        .video-duration .badge {
            font-size: 0.6rem;
            padding: 1px 4px;
        }
        
        .play-button i {
            font-size: 2rem !important;
        }

        /* Melhorar layout em telas pequenas */
        .video-thumbnail-container {
            margin-bottom: 15px;
        }
    }

    /* === RESPONSIVIDADE PARA TABLETS === */
    @media (max-width: 992px) and (min-width: 769px) {
        .video-thumbnail-container.aspect-9-16 {
            max-height: 350px;
        }
        
        .video-thumbnail-container.aspect-3-4 {
            max-height: 320px;
        }
    }

    /* === LAYOUT ESPECIAL PARA VÍDEOS VERTICAIS EM GRID === */
    @media (min-width: 992px) {
        /* Em telas grandes, vídeos verticais devem ter colunas menores */
        .col-lg-3:has(.video-thumbnail-container.aspect-9-16),
        .col-lg-3:has(.video-thumbnail-container.aspect-3-4) {
            flex: 0 0 25%;
            max-width: 25%;
        }
        
        /* Vídeos quadrados podem ser um pouco maiores */
        .col-lg-3:has(.video-thumbnail-container.aspect-1-1) {
            flex: 0 0 33.333%;
            max-width: 33.333%;
        }
    }
    
    /* === ESTILOS PARA IMAGENS MODERNAS === */
    
    .image-container-modern {
        cursor: pointer;
        border-radius: 12px;
        overflow: hidden;
        background: #f8f9fa;
        aspect-ratio: 1;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .image-container-modern:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.2);
    }
    
    .image-modern {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
        border-radius: 0;
    }
    
    .image-container-modern:hover .image-modern {
        transform: scale(1.1);
    }
    
    .image-overlay-modern {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
        backdrop-filter: blur(1px);
    }
    
    .image-container-modern:hover .image-overlay-modern {
        opacity: 1;
    }
    
    .image-overlay-content {
        text-align: center;
        transition: transform 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        text-shadow: 0 2px 8px rgba(0,0,0,0.8);
    }
    
    .image-container-modern:hover .image-overlay-content {
        transform: scale(1.1);
    }
    
    .image-number {
        position: absolute;
        top: 8px;
        right: 8px;
        z-index: 2;
    }
    
    .image-number .badge {
        font-size: 0.7rem;
        padding: 3px 7px;
        background: rgba(0,0,0,0.8) !important;
        border: 1px solid rgba(255,255,255,0.3);
    }
    
    .image-shimmer-effect {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.2) 50%, transparent 70%);
        transform: translateX(-100%);
        transition: transform 0.6s ease;
    }
    
    .image-container-modern:hover .image-shimmer-effect {
        transform: translateX(100%);
    }
    
    .more-images-container {
        cursor: pointer;
        border-radius: 12px;
        aspect-ratio: 1;
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    }
    
    .more-images-container:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 24px rgba(40, 167, 69, 0.4);
        background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
    }
    
    .more-images-content {
        text-align: center;
        transition: transform 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        text-shadow: 0 2px 8px rgba(0,0,0,0.5);
    }
    
    .more-images-container:hover .more-images-content {
        transform: scale(1.1);
    }
    
    /* Media Section Headers */
    .media-section {
        border-left: 4px solid #007bff;
        padding-left: 20px;
        margin-bottom: 20px;
        background: linear-gradient(90deg, rgba(0,123,255,0.05) 0%, transparent 100%);
        border-radius: 0 8px 8px 0;
        padding: 15px 0 15px 20px;
    }
    
    .media-section:last-child {
        border-left-color: #6c757d;
        background: linear-gradient(90deg, rgba(108,117,125,0.05) 0%, transparent 100%);
    }
    
    .media-section:nth-child(2) {
        border-left-color: #28a745;
        background: linear-gradient(90deg, rgba(40,167,69,0.05) 0%, transparent 100%);
    }
    
    .media-section:nth-child(3) {
        border-left-color: #dc3545;
        background: linear-gradient(90deg, rgba(220,53,69,0.05) 0%, transparent 100%);
    }
    
    /* === MODAL CUSTOMIZADO PARA IMAGENS === */
    .custom-image-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.3s ease;
        opacity: 1;
        transition: opacity 0.2s ease;
    }
    
    .custom-modal-backdrop {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        backdrop-filter: blur(2px);
    }
    
    .custom-modal-content {
        position: relative;
        max-width: 95vw;
        max-height: 95vh;
        background: #000;
        border-radius: 12px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.5);
        cursor: default;
        overflow: hidden;
        animation: scaleIn 0.3s ease;
        display: flex;
        flex-direction: column;
    }
    
    .custom-modal-header {
        position: absolute;
        top: 0;
        right: 0;
        z-index: 10;
        padding: 10px;
        display: flex;
        gap: 8px;
    }
    
    .custom-close-btn,
    .custom-zoom-btn {
        background: rgba(0,0,0,0.8);
        color: white;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        font-size: 18px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        backdrop-filter: blur(4px);
    }
    
    .custom-close-btn:hover {
        background: rgba(220, 53, 69, 0.8);
        transform: scale(1.1);
    }
    
    .custom-zoom-btn:hover {
        background: rgba(0, 123, 255, 0.8);
        transform: scale(1.1);
    }
    
    .custom-modal-body {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        min-height: 300px;
    }
    
    .custom-modal-image {
        max-width: 100%;
        max-height: 85vh;
        object-fit: contain;
        display: block;
        cursor: zoom-in;
        transition: transform 0.3s ease;
    }
    
    .image-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    
    .image-error {
        max-width: 400px;
    }
    
    .custom-modal-footer {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 15px;
        text-align: center;
        backdrop-filter: blur(4px);
    }
    
    .modal-actions {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    
    /* Estilos específicos para o SweetAlert2 das imagens */
    .swal-image-modal {
        border-radius: 15px !important;
        padding: 0 !important;
    }
    
    .swal-full-image {
        border-radius: 8px;
        box-shadow: none !important;
    }
    
    .swal-image-modal .swal2-popup {
        background: #000 !important;
        padding: 10px !important;
    }
    
    .swal-image-modal .swal2-close {
        background: rgba(255,255,255,0.1) !important;
        border-radius: 50% !important;
        width: 40px !important;
        height: 40px !important;
        color: white !important;
        font-size: 24px !important;
        transition: all 0.3s ease !important;
    }
    
    .swal-image-modal .swal2-close:hover {
        background: rgba(220, 53, 69, 0.8) !important;
        transform: scale(1.1) !important;
    }

    /* Estilos aprimorados para modal de imagens */
    .swal-image-modal-enhanced {
        border-radius: 20px !important;
        padding: 0 !important;
        overflow: hidden !important;
        box-shadow: 0 20px 60px rgba(0,0,0,0.8) !important;
    }
    
    .swal-image-modal-enhanced .swal2-popup {
        background: #000 !important;
        padding: 0 !important;
        margin: 0 !important;
        border-radius: 20px !important;
    }
    
    .swal-full-image-enhanced {
        border-radius: 0 !important;
        transition: transform 0.3s ease !important;
        user-select: none;
    }
    
    .swal-image-modal-enhanced .swal2-close {
        background: rgba(0,0,0,0.7) !important;
        border: 2px solid rgba(255,255,255,0.3) !important;
        border-radius: 50% !important;
        width: 44px !important;
        height: 44px !important;
        color: white !important;
        font-size: 20px !important;
        font-weight: bold !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        backdrop-filter: blur(4px) !important;
        z-index: 1000 !important;
        top: 15px !important;
        right: 15px !important;
    }
    
    .swal-image-modal-enhanced .swal2-close:hover {
        background: rgba(220, 53, 69, 0.9) !important;
        border-color: rgba(220, 53, 69, 0.6) !important;
        transform: scale(1.15) rotate(90deg) !important;
        box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4) !important;
    }

    .swal-image-modal-enhanced .swal2-actions {
        margin: 0 !important;
        padding: 15px 20px !important;
        background: rgba(0,0,0,0.8) !important;
        backdrop-filter: blur(8px) !important;
        justify-content: center !important;
    }

    .swal-image-modal-enhanced .swal2-cancel {
        background: rgba(0, 123, 255, 0.8) !important;
        border: 1px solid rgba(0, 123, 255, 0.6) !important;
        color: white !important;
        border-radius: 25px !important;
        padding: 8px 20px !important;
        font-size: 14px !important;
        transition: all 0.3s ease !important;
        backdrop-filter: blur(4px) !important;
    }

    .swal-image-modal-enhanced .swal2-cancel:hover {
        background: rgba(0, 123, 255, 1) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4) !important;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes scaleIn {
        from { transform: scale(0.8); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    
    /* === PLAYER INLINE DE VÍDEO === */
    .playing-video {
        background: #000 !important;
    }
    
    .playing-video .video-thumbnail-container {
        border: 2px solid #28a745;
        box-shadow: 0 0 20px rgba(40, 167, 69, 0.4);
    }
    
    .inline-video-player {
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }
    
    .inline-video-close {
        transition: all 0.3s ease !important;
        backdrop-filter: blur(4px);
    }
    
    .inline-video-close:hover {
        transform: scale(1.2) rotate(90deg) !important;
    }
    
    .inline-video-quality {
        backdrop-filter: blur(4px);
        transition: opacity 0.5s ease;
    }
    
    .inline-video-quality .badge {
        font-size: 0.7rem;
        padding: 4px 8px;
        border-radius: 12px;
        backdrop-filter: blur(4px);
    }
    
    /* Animação para quando o vídeo está carregando */
    .inline-video-player:not([src]) {
        background: linear-gradient(45deg, #000, #333, #000);
        background-size: 400% 400%;
        animation: gradientShift 2s ease infinite;
    }
    
    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    
    /* Mobile adaptations */
    @media (max-width: 768px) {
        .custom-modal-content {
            max-width: 95vw;
            max-height: 95vh;
            margin: 20px;
        }
        
        .custom-close-btn {
            width: 36px;
            height: 36px;
            font-size: 20px;
        }
        
        .inline-video-close {
            width: 28px !important;
            height: 28px !important;
            font-size: 16px !important;
            top: 4px !important;
            right: 4px !important;
        }
        
        .inline-video-quality {
            top: 4px !important;
            left: 4px !important;
        }
        
        .inline-video-quality .badge {
            font-size: 0.6rem;
            padding: 2px 6px;
        }
    }
    
    /* === ESTILOS PARA FOTO DE PERFIL === */
    .profile-pic-container {
        position: relative;
        flex-shrink: 0;
    }
    
    .profile-pic {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        background: #f8f9fa;
    }
    
    .profile-pic.profile-pic-clickable {
        cursor: pointer;
    }
    
    .profile-pic:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-color: #007bff;
    }
    
    .profile-pic-clickable:hover::after {
        content: '🔍';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0,0,0,0.8);
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        opacity: 0;
        animation: fadeIn 0.3s ease forwards;
    }
    
    @keyframes fadeIn {
        to { opacity: 1; }
    }
    
    .profile-pic-fallback {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        border: 2px solid #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    @media (max-width: 768px) {
        .profile-pic,
        .profile-pic-fallback {
            width: 40px;
            height: 40px;
        }
        
        .profile-pic-fallback i {
            font-size: 16px;
        }
    }
</style>
@endsection
