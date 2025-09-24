<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;
use App\Models\Cliente;
use App\Models\MidiasSocialMonitoramento;
use App\Models\MidiasSociaisColeta;
use App\Models\NoticiaMidiaSocial;

class MidiasSociaisController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        Session::put('url', 'midias-sociais');
    }

    // === MONITORAMENTOS ===
    
    /**
     * Listagem de monitoramentos
     */
    public function indexMonitoramentos(Request $request)
    {
        Session::put('sub-menu', 'midias-sociais-monitoramentos');
        
        $query = MidiasSocialMonitoramento::with(['cliente', 'coletas']);
        
        // Aplicar filtros
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }
        
        if ($request->filled('tipo_midia')) {
            $query->where('tipo_midia', 'like', '%' . $request->tipo_midia . '%');
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('nome')) {
            $query->where('nome', 'like', '%' . $request->nome . '%');
        }
        
        $monitoramentos = $query->orderBy('data_criacao', 'desc')->get();
        $clientes = Cliente::where('fl_ativo', true)->orderBy('nome')->get();
        
        return view('midias-sociais/monitoramentos/index', compact('monitoramentos', 'clientes'));
    }
    
    /**
     * Formulário para novo monitoramento
     */
    public function criarMonitoramento()
    {
        Session::put('sub-menu', 'midias-sociais-monitoramentos');
        
        $clientes = Cliente::where('fl_ativo', true)->orderBy('nome')->get();
        
        return view('midias-sociais/monitoramentos/novo', compact('clientes'));
    }
    
    /**
     * Salvar novo monitoramento
     */
    public function salvarMonitoramento(Request $request)
    {
        try {
            // Validação
            $request->validate([
                'nome' => 'required|string|max:255',
                'cliente_id' => 'required|integer|exists:clientes,id',
                'tipos_midia' => 'required|array|min:1',
                'palavras_chave' => 'required|string'
            ], [
                'nome.required' => 'O nome do monitoramento é obrigatório.',
                'cliente_id.required' => 'Selecione um cliente.',
                'cliente_id.exists' => 'Cliente selecionado não existe.',
                'tipos_midia.required' => 'Selecione pelo menos uma rede social.',
                'tipos_midia.min' => 'Selecione pelo menos uma rede social.',
                'palavras_chave.required' => 'Informe pelo menos uma palavra-chave.'
            ]);
            
            // Processar palavras-chave
            $palavrasChave = array_filter(
                array_map('trim', explode(',', $request->palavras_chave))
            );
            
            // Processar palavras de exclusão
            $palavrasExclusao = [];
            if ($request->filled('palavras_exclusao')) {
                $palavrasExclusao = array_filter(
                    array_map('trim', explode(',', $request->palavras_exclusao))
                );
            }
            
            $monitoramento = MidiasSocialMonitoramento::create([
                'nome' => $request->nome,
                'descricao' => $request->descricao,
                'cliente_id' => $request->cliente_id,
                'tipo_midia' => implode(',', $request->tipos_midia), // Salva como string separada por vírgula
                'palavras_chave' => $palavrasChave,
                'status' => $request->status ?? 'ativo',
                'configuracoes' => [
                    'limite_posts_dia' => (int) ($request->limite_posts_dia ?? 100),
                    'min_likes' => (int) ($request->min_likes ?? 0),
                    'min_shares' => (int) ($request->min_shares ?? 0),
                    'relevancia_minima' => (float) ($request->relevancia_minima ?? 0),
                    'incluir_imagens' => $request->has('incluir_imagens'),
                    'incluir_videos' => $request->has('incluir_videos'),
                    'incluir_links' => $request->has('incluir_links'),
                    'palavras_exclusao' => $palavrasExclusao,
                    'idiomas' => $request->idiomas ?? []
                ]
            ]);
            
            Flash::success('Monitoramento "' . $monitoramento->nome . '" criado com sucesso!');
            return redirect()->route('midias-sociais.monitoramentos.index');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Flash::error('Erro ao criar monitoramento: ' . $e->getMessage());
            return back()->withInput();
        }
    }
    
    /**
     * Formulário para editar monitoramento
     */
    public function editarMonitoramento($id)
    {
        Session::put('sub-menu', 'midias-sociais-monitoramentos');
        
        $monitoramento = MidiasSocialMonitoramento::with('cliente')->findOrFail($id);
        $clientes = Cliente::where('fl_ativo', true)->orderBy('nome')->get();
        $estatisticas = $this->getEstatisticasMonitoramento($id);
        
        return view('midias-sociais/monitoramentos/editar', compact('monitoramento', 'clientes', 'estatisticas'));
    }
    
    /**
     * Atualizar monitoramento
     */
    public function atualizarMonitoramento(Request $request, $id)
    {
        try {
            // Validação
            $request->validate([
                'nome' => 'required|string|max:255',
                'cliente_id' => 'required|integer',
                'tipos_midia' => 'required|array|min:1',
                'palavras_chave' => 'required|string'
            ]);
            
            // Processar palavras-chave
            $palavrasChave = array_filter(array_map('trim', explode(',', $request->palavras_chave)));
            
            // Processar palavras de exclusão
            $palavrasExclusao = [];
            if ($request->filled('palavras_exclusao')) {
                $palavrasExclusao = array_filter(array_map('trim', explode(',', $request->palavras_exclusao)));
            }
            
            $monitoramento = MidiasSocialMonitoramento::findOrFail($id);
            $monitoramento->update([
                'nome' => $request->nome,
                'descricao' => $request->descricao,
                'cliente_id' => $request->cliente_id,
                'tipo_midia' => implode(',', $request->tipos_midia),
                'palavras_chave' => $palavrasChave,
                'status' => $request->status ?? 'ativo',
                'configuracoes' => [
                    'limite_posts_dia' => (int) ($request->limite_posts_dia ?? 100),
                    'min_likes' => (int) ($request->min_likes ?? 0),
                    'min_shares' => (int) ($request->min_shares ?? 0),
                    'relevancia_minima' => (float) ($request->relevancia_minima ?? 0),
                    'incluir_imagens' => $request->has('incluir_imagens'),
                    'incluir_videos' => $request->has('incluir_videos'),
                    'incluir_links' => $request->has('incluir_links'),
                    'palavras_exclusao' => $palavrasExclusao,
                    'idiomas' => $request->idiomas ?? []
                ],
                'data_atualizacao' => now()
            ]);
            
            Flash::success('Monitoramento "' . $monitoramento->nome . '" atualizado com sucesso!');
            return redirect()->route('midias-sociais.monitoramentos.index');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Flash::error('Erro ao atualizar monitoramento: ' . $e->getMessage());
            return back()->withInput();
        }
    }
    
    /**
     * Alternar status do monitoramento
     */
    public function toggleStatus($id)
    {
        try {
            $monitoramento = MidiasSocialMonitoramento::findOrFail($id);
            $novoStatus = $monitoramento->status == 'ativo' ? 'pausado' : 'ativo';
            $monitoramento->update([
                'status' => $novoStatus,
                'data_atualizacao' => now()
            ]);
            
            return response()->json([
                'success' => true, 
                'message' => 'Status alterado para ' . $novoStatus . ' com sucesso!',
                'novo_status' => $novoStatus
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Excluir monitoramento
     */
    public function excluirMonitoramento($id)
    {
        try {
            $monitoramento = MidiasSocialMonitoramento::findOrFail($id);
            $nomeMonitoramento = $monitoramento->nome;
            
            // Excluir todos os posts coletados relacionados
            MidiasSociaisColeta::where('monitoramento_id', $id)->delete();
            
            // Excluir o monitoramento
            $monitoramento->delete();
            
            return response()->json([
                'success' => true, 
                'message' => 'Monitoramento "' . $nomeMonitoramento . '" excluído com sucesso!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Resetar coletas do monitoramento
     */
    public function resetarColetas($id)
    {
        try {
            $monitoramento = MidiasSocialMonitoramento::findOrFail($id);
            
            // Contar posts antes de deletar para informar ao usuário
            $quantidadePosts = MidiasSociaisColeta::where('monitoramento_id', $id)->count();
            
            // Excluir todos os posts coletados do monitoramento
            MidiasSociaisColeta::where('monitoramento_id', $id)->delete();
            
            // Resetar data da última coleta
            $monitoramento->update([
                'ultima_coleta' => null,
                'data_atualizacao' => now()
            ]);
            
            return response()->json([
                'success' => true, 
                'message' => 'Coletas resetadas com sucesso! ' . $quantidadePosts . ' posts foram removidos.',
                'posts_removidos' => $quantidadePosts
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    // === POSTS COLETADOS ===
    
    /**
     * Listagem de posts coletados
     */
    public function indexPosts(Request $request)
    {
        Session::put('sub-menu', 'midias-sociais-posts');
        
        $query = MidiasSociaisColeta::with(['monitoramento', 'monitoramento.cliente', 'cliente']);
        
        // Aplicar filtros
        if ($request->filled('monitoramento_id')) {
            $query->where('monitoramento_id', $request->monitoramento_id);
        }
        
        if ($request->filled('tipo_midia')) {
            $query->where('tipo_midia', $request->tipo_midia);
        }
        
        if ($request->filled('data_inicial')) {
            $query->whereDate('data_publicacao', '>=', $request->data_inicial);
        }
        
        if ($request->filled('data_final')) {
            $query->whereDate('data_publicacao', '<=', $request->data_final);
        }
        
        if ($request->filled('palavra_chave')) {
            $query->where('texto', 'like', '%' . $request->palavra_chave . '%');
        }
        
        if ($request->filled('min_likes')) {
            $query->where('likes', '>=', $request->min_likes);
        }
        
        if ($request->filled('min_shares')) {
            $query->where('shares', '>=', $request->min_shares);
        }
        
        if ($request->filled('relevancia')) {
            switch ($request->relevancia) {
                case 'alta':
                    $query->where('relevancia_score', '>=', 0.7);
                    break;
                case 'media':
                    $query->whereBetween('relevancia_score', [0.3, 0.7]);
                    break;
                case 'baixa':
                    $query->where('relevancia_score', '<', 0.3);
                    break;
            }
        }
        
        if ($request->filled('com_midia')) {
            switch ($request->com_midia) {
                case 'imagem':
                    $query->where('tem_imagem', true);
                    break;
                case 'video':
                    $query->where('tem_video', true);
                    break;
                case 'sem_midia':
                    $query->where('tem_imagem', false)->where('tem_video', false);
                    break;
            }
        }
        
        // Ordenação
        switch ($request->ordenar ?? 'data_desc') {
            case 'data_asc':
                $query->orderBy('data_publicacao', 'asc');
                break;
            case 'likes_desc':
                $query->orderBy('likes', 'desc');
                break;
            case 'relevancia_desc':
                $query->orderBy('relevancia_score', 'desc');
                break;
            default:
                $query->orderBy('data_publicacao', 'desc');
        }
        
        $posts = $query->paginate($request->per_page ?? 50);
        $monitoramentos = MidiasSocialMonitoramento::with('cliente')->where('status', 'ativo')->orderBy('nome')->get();
        $estatisticas = $this->getEstatisticasPosts();
        
        return view('midias-sociais/posts/index', compact('posts', 'monitoramentos', 'estatisticas'));
    }
    
    /**
     * Detalhes de um post
     */
    public function detalhesPost($id)
    {
        try {
            $post = MidiasSociaisColeta::with(['monitoramento', 'monitoramento.cliente'])->findOrFail($id);
            
            $html = '<div class="post-details p-3">
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="mb-3"><i class="fa fa-info-circle"></i> Detalhes Completos do Post</h6>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <strong>ID:</strong> ' . $post->id . '
                                </div>
                                <div class="mb-3">
                                    <strong>Rede Social:</strong> 
                                    <span class="badge badge-' . $post->tipo_cor . '">
                                        <i class="fa ' . $post->tipo_icone . '"></i> ' . ucfirst($post->tipo_midia) . '
                                    </span>
                                </div>
                                <div class="mb-3">
                                    <strong>Autor:</strong> ' . ($post->autor_display ?: 'Não informado') . '
                                </div>
                                <div class="mb-3">
                                    <strong>Data de Publicação:</strong> ' . ($post->data_publicacao ? $post->data_publicacao->format('d/m/Y \à\s H:i') : 'Não informada') . '
                                </div>
                                <div class="mb-3">
                                    <strong>Data de Coleta:</strong> ' . ($post->data_coleta ? $post->data_coleta->format('d/m/Y \à\s H:i') : 'Não informada') . '
                                </div>
                                ' . ($post->monitoramento ? '
                                <div class="mb-3">
                                    <strong>Monitoramento:</strong> ' . $post->monitoramento->nome . '
                                    ' . ($post->monitoramento->cliente ? '<br><small class="text-muted">Cliente: ' . $post->monitoramento->cliente->nome . '</small>' : '') . '
                                </div>' : '') . '
                                ' . ($post->relevancia_score ? '
                                <div class="mb-3">
                                    <strong>Relevância:</strong> 
                                    <span class="badge badge-' . ($post->relevancia_score >= 0.7 ? 'success' : ($post->relevancia_score >= 0.4 ? 'warning' : 'secondary')) . '">
                                        ' . $post->relevancia_percentual . '%
                                    </span>
                                </div>' : '') . '
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <strong>Métricas:</strong>
                                    <ul class="list-unstyled mt-2">
                                        <li><i class="fa fa-heart text-danger"></i> ' . number_format($post->likes ?? 0) . ' curtidas</li>
                                        <li><i class="fa fa-share text-success"></i> ' . number_format($post->shares ?? 0) . ' compartilhamentos</li>
                                        <li><i class="fa fa-comment text-primary"></i> ' . number_format($post->comentarios ?? 0) . ' comentários</li>
                                        ' . ($post->views ? '<li><i class="fa fa-eye text-info"></i> ' . number_format($post->views) . ' visualizações</li>' : '') . '
                                    </ul>
                                </div>
                                
                                ' . ($post->hashtags ? '
                                <div class="mb-3">
                                    <strong>Hashtags:</strong><br>
                                    ' . implode(' ', array_map(function($tag) { 
                                        return '<span class="badge badge-pill badge-info">#' . $tag . '</span>'; 
                                    }, $post->hashtags)) . '
                                </div>' : '') . '
                                
                                ' . ($post->mencoes ? '
                                <div class="mb-3">
                                    <strong>Menções:</strong><br>
                                    ' . implode(' ', array_map(function($mencao) { 
                                        return '<span class="badge badge-pill badge-light">@' . $mencao . '</span>'; 
                                    }, $post->mencoes)) . '
                                </div>' : '') . '
                                
                                ' . ($post->url_post ? '
                                <div class="mb-3">
                                    <strong>Link Original:</strong><br>
                                    <a href="' . $post->url_post . '" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fa fa-external-link"></i> Abrir Post Original
                                    </a>
                                </div>' : '') . '
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <strong>Texto Completo:</strong>
                                    <div class="border rounded p-3 mt-2" style="background-color: #f8f9fa; max-height: 200px; overflow-y: auto;">
                                        ' . ($post->texto ? nl2br(e($post->texto)) : '<em class="text-muted">Sem conteúdo de texto disponível</em>') . '
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        ' . ($post->tem_midia && $post->urls_midia ? '
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <strong>Mídia Anexa:</strong>
                                    <div class="row mt-2">
                                        ' . implode('', array_map(function($url, $index) {
                                            if (strpos($url, 'jpg') !== false || strpos($url, 'jpeg') !== false || strpos($url, 'png') !== false || strpos($url, 'gif') !== false) {
                                                return '<div class="col-md-4 mb-2">
                                                    <img src="' . $url . '" class="img-fluid rounded" alt="Mídia" style="cursor: pointer; max-height: 100px; width: 100%; object-fit: cover;" onclick="abrirImagem(\'' . $url . '\')">
                                                </div>';
                                            } else {
                                                return '<div class="col-md-4 mb-2">
                                                    <div class="border rounded p-2 text-center">
                                                        <i class="fa fa-file-o fa-2x"></i><br>
                                                        <small>Arquivo de mídia</small>
                                                    </div>
                                                </div>';
                                            }
                                        }, $post->urls_midia, array_keys($post->urls_midia))) . '
                                    </div>
                                </div>
                            </div>
                        </div>' : '') . '
                        
                        ' . ($post->misc_data ? '
                        <div class="row">
                            <div class="col-md-12">
                                <details class="mb-3">
                                    <summary><strong>Dados Técnicos</strong></summary>
                                    <div class="border rounded p-2 mt-2" style="background-color: #f8f9fa; max-height: 150px; overflow-y: auto;">
                                        <small><pre>' . json_encode($post->misc_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre></small>
                                    </div>
                                </details>
                            </div>
                        </div>' : '') . '
                    </div>';
            
            return response($html);
            
        } catch (\Exception $e) {
            return response('<div class="alert alert-danger text-center">
                <i class="fa fa-exclamation-triangle"></i>
                <strong>Erro ao carregar detalhes</strong><br>
                <small>' . $e->getMessage() . '</small>
            </div>', 500);
        }
    }
    
    /**
     * Exportar posts
     */
    public function exportarPosts(Request $request)
    {
        try {
            $formato = $request->get('formato', 'excel');
            
            // Aqui você implementará a exportação
            /*
            $posts = MidiasSociaisColeta::with(['monitoramento', 'monitoramento.cliente']);
            
            // Aplicar mesmos filtros da listagem...
            
            switch ($formato) {
                case 'excel':
                    return Excel::download(new PostsExport($posts->get()), 'posts-midias-sociais.xlsx');
                case 'csv':
                    return Excel::download(new PostsExport($posts->get()), 'posts-midias-sociais.csv');
                case 'pdf':
                    return PDF::loadView('midias-sociais.posts.pdf', compact('posts'))->download('posts-midias-sociais.pdf');
            }
            */
            
            Flash::info('Funcionalidade de exportação será implementada em breve.');
            return back();
            
        } catch (\Exception $e) {
            Flash::error('Erro ao exportar: ' . $e->getMessage());
            return back();
        }
    }
    
    
    /**
     * Método auxiliar para estatísticas gerais de posts
     */
    private function getEstatisticasPosts()
    {
        return [
            'total_posts' => MidiasSociaisColeta::count(),
            'posts_hoje' => MidiasSociaisColeta::whereDate('data_coleta', today())->count(),
            'total_likes' => MidiasSociaisColeta::sum('likes'),
            'total_shares' => MidiasSociaisColeta::sum('shares'),
            'total_comentarios' => MidiasSociaisColeta::sum('comentarios'),
            'monitoramentos_ativos' => MidiasSocialMonitoramento::where('status', 'ativo')->count()
        ];
    }
    
    /**
     * Obter estatísticas específicas de um monitoramento
     */
    private function getEstatisticasMonitoramento($id)
    {
        return [
            'total_posts' => MidiasSociaisColeta::where('monitoramento_id', $id)->count(),
            'posts_hoje' => MidiasSociaisColeta::where('monitoramento_id', $id)->whereDate('data_coleta', today())->count(),
            'ultima_coleta' => MidiasSociaisColeta::where('monitoramento_id', $id)->latest('data_coleta')->value('data_coleta'),
            'total_likes' => MidiasSociaisColeta::where('monitoramento_id', $id)->sum('likes'),
            'total_shares' => MidiasSociaisColeta::where('monitoramento_id', $id)->sum('shares'),
            'total_comentarios' => MidiasSociaisColeta::where('monitoramento_id', $id)->sum('comentarios')
        ];
    }
    
    // === NOTÍCIAS ===
    
    /**
     * Criar notícias a partir dos posts selecionados
     */
    public function criarNoticias(Request $request)
    {
        try {
            $request->validate([
                'posts_ids' => 'required|array|min:1',
                'posts_ids.*' => 'integer|exists:midias_sociais_coletas,id'
            ], [
                'posts_ids.required' => 'Selecione pelo menos um post.',
                'posts_ids.min' => 'Selecione pelo menos um post.',
                'posts_ids.*.exists' => 'Um ou mais posts selecionados não existem.'
            ]);
            
            $noticiasIds = NoticiaMidiaSocial::criarDoPosts($request->posts_ids);
            
            if (count($noticiasIds) > 0) {
                $message = 'Criadas ' . count($noticiasIds) . ' notícias com sucesso!';
                
                // Retornar JSON para AJAX
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => mb_convert_encoding($message, 'UTF-8', 'UTF-8'),
                        'noticias_criadas' => count($noticiasIds),
                        'redirect' => route('midias-sociais.noticias.index')
                    ], 200, ['Content-Type' => 'application/json; charset=utf-8']);
                }
                
                Flash::success($message);
                
                // Redirecionar para a listagem de notícias se houver mais de 1, 
                // senão redirecionar para os detalhes da notícia criada
                if (count($noticiasIds) > 1) {
                    return redirect()->route('midias-sociais.noticias.index');
                } else {
                    return redirect()->route('midias-sociais.noticias.index')->with('highlight', $noticiasIds[0]);
                }
            } else {
                $message = 'Nenhuma notícia foi criada. Os posts selecionados podem já ter sido convertidos em notícias.';
                
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => mb_convert_encoding($message, 'UTF-8', 'UTF-8'),
                        'noticias_criadas' => 0
                    ], 422, ['Content-Type' => 'application/json; charset=utf-8']);
                }
                
                Flash::warning($message);
                return back();
            }
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMessage = 'Erro de validação: ' . implode(', ', $e->errors()['posts_ids'] ?? []);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => mb_convert_encoding($errorMessage, 'UTF-8', 'UTF-8'),
                    'errors' => $e->errors()
                ], 422, ['Content-Type' => 'application/json; charset=utf-8']);
            }
            
            Flash::error($errorMessage);
            return back();
        } catch (\Exception $e) {
            $errorMessage = 'Erro ao criar notícias: ' . $e->getMessage();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => mb_convert_encoding($errorMessage, 'UTF-8', 'UTF-8'),
                    'error_details' => mb_convert_encoding($e->getMessage(), 'UTF-8', 'UTF-8')
                ], 500, ['Content-Type' => 'application/json; charset=utf-8']);
            }
            
            Flash::error($errorMessage);
            return back();
        }
    }
    
    /**
     * Listagem de notícias de mídias sociais
     */
    public function indexNoticias(Request $request)
    {
        Session::put('sub-menu', 'midias-sociais-noticias');
        
        $query = NoticiaMidiaSocial::with(['postOriginal', 'postOriginal.monitoramento', 'postOriginal.cliente', 'clientes']);
        
        // Aplicar filtros
        if ($request->filled('cliente_id')) {
            $query->whereHas('clientes', function($q) use ($request) {
                $q->where('cliente_id', $request->cliente_id);
            });
        }
        
        if ($request->filled('rede_social')) {
            $query->where('rede_social', $request->rede_social);
        }
        
        if ($request->filled('sentimento')) {
            $query->where('sentimento_inicial', $request->sentimento);
        }
        
        if ($request->filled('data_inicial')) {
            $query->whereDate('data_publicacao', '>=', $request->data_inicial);
        }
        
        if ($request->filled('data_final')) {
            $query->whereDate('data_publicacao', '<=', $request->data_final);
        }
        
        if ($request->filled('palavra_chave')) {
            $palavraChave = $request->palavra_chave;
            $query->where(function($q) use ($palavraChave) {
                $q->where('titulo', 'like', "%{$palavraChave}%")
                  ->orWhere('resumo', 'like', "%{$palavraChave}%")
                  ->orWhere('autor_nome', 'like', "%{$palavraChave}%")
                  ->orWhere('autor_username', 'like', "%{$palavraChave}%");
            });
        }
        
        // Ordenação
        switch ($request->ordenar ?? 'data_desc') {
            case 'data_asc':
                $query->orderBy('data_publicacao', 'asc');
                break;
            case 'valor_desc':
                $query->orderBy('valor_retorno', 'desc');
                break;
            case 'titulo_asc':
                $query->orderBy('titulo', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc'); // Por ordem de criação da notícia
        }
        
        $noticias = $query->paginate($request->per_page ?? 20);
        $clientes = Cliente::where('fl_ativo', true)->orderBy('nome')->get();
        $estatisticas = $this->getEstatisticasNoticias();
        
        return view('midias-sociais/noticias/index', compact('noticias', 'clientes', 'estatisticas'));
    }
    
    /**
     * Detalhes de uma notícia
     */
    public function detalhesNoticia($id)
    {
        try {
            $noticia = NoticiaMidiaSocial::with([
                'postOriginal', 
                'postOriginal.monitoramento', 
                'postOriginal.cliente', 
                'clientes',
                'usuario'
            ])->findOrFail($id);
            
            return view('midias-sociais/noticias/detalhes', compact('noticia'));
            
        } catch (\Exception $e) {
            Flash::error('Notícia não encontrada.');
            return redirect()->route('midias-sociais.noticias.index');
        }
    }
    
    /**
     * Remover uma notícia (soft delete)
     */
    public function removerNoticia($id)
    {
        try {
            $noticia = NoticiaMidiaSocial::findOrFail($id);
            
            // Remover vinculação com clientes
            \App\Models\NoticiaCliente::where('noticia_id', $id)->where('tipo_id', 5)->delete();
            
            // Marcar post original como não processado novamente
            if ($noticia->postOriginal) {
                $noticia->postOriginal->update(['processado' => false]);
            }
            
            $noticia->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Notícia removida com sucesso!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover notícia: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Estatísticas das notícias
     */
    private function getEstatisticasNoticias()
    {
        return [
            'total_noticias' => NoticiaMidiaSocial::count(),
            'noticias_hoje' => NoticiaMidiaSocial::whereDate('created_at', today())->count(),
            'valor_total' => NoticiaMidiaSocial::sum('valor_retorno'),
            'clientes_atingidos' => \App\Models\NoticiaCliente::where('tipo_id', 5)->distinct('cliente_id')->count(),
            'por_rede' => [
                'twitter' => NoticiaMidiaSocial::where('rede_social', 'twitter')->count(),
                'linkedin' => NoticiaMidiaSocial::where('rede_social', 'linkedin')->count(),
                'facebook' => NoticiaMidiaSocial::where('rede_social', 'facebook')->count(),
                'instagram' => NoticiaMidiaSocial::where('rede_social', 'instagram')->count()
            ]
        ];
    }
}
