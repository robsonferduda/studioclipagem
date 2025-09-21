<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NoticiaMidiaSocial extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'noticia_midia_social';
    
    protected $fillable = [
        'post_coleta_id',
        'titulo',
        'resumo', 
        'rede_social',
        'autor_nome',
        'autor_username',
        'data_publicacao',
        'url_post',
        'metricas_engagement',
        'sentimento_inicial',
        'valor_retorno',
        'cd_usuario',
        'misc_data'
    ];

    protected $casts = [
        'metricas_engagement' => 'array',
        'misc_data' => 'array',
        'data_publicacao' => 'datetime',
        'sentimento_inicial' => 'integer'
    ];

    // Relacionamentos
    public function postOriginal()
    {
        return $this->belongsTo(MidiasSociaisColeta::class, 'post_coleta_id');
    }

    public function usuario()
    {
        return $this->hasOne(\App\User::class, 'id', 'cd_usuario');
    }

    public function clientes()
    {
        return $this->belongsToMany(Cliente::class, 'noticia_cliente', 'noticia_id', 'cliente_id')
                    ->withPivot('id', 'tipo_id', 'sentimento', 'area', 'fl_enviada', 'monitoramento_id')
                    ->where('tipo_id', 5) // Tipo 5 para mídias sociais
                    ->withTimestamps();
    }

    // Accessors
    public function getRedeIconeAttribute()
    {
        $icones = [
            'twitter' => 'fa-twitter',
            'linkedin' => 'fa-linkedin', 
            'facebook' => 'fa-facebook',
            'instagram' => 'fa-instagram'
        ];

        return $icones[$this->rede_social] ?? 'fa-share-alt';
    }

    public function getRedeCorAttribute()
    {
        $cores = [
            'twitter' => 'info',
            'linkedin' => 'primary',
            'facebook' => 'primary',
            'instagram' => 'danger'
        ];

        return $cores[$this->rede_social] ?? 'secondary';
    }

    public function getAutorDisplayAttribute()
    {
        if ($this->autor_username) {
            return '@' . $this->autor_username;
        }
        return $this->autor_nome ?: 'Usuário não identificado';
    }

    public function getSentimentoTextoAttribute()
    {
        $sentimentos = [
            1 => 'Positivo',
            0 => 'Neutro',
            -1 => 'Negativo'
        ];

        return $sentimentos[$this->sentimento_inicial] ?? 'Não definido';
    }

    public function getSentimentoCorAttribute()
    {
        $cores = [
            1 => 'success',
            0 => 'secondary', 
            -1 => 'danger'
        ];

        return $cores[$this->sentimento_inicial] ?? 'light';
    }

    // Métodos estáticos para criação
    public static function criarDoPosts($postsIds)
    {
        $posts = MidiasSociaisColeta::with(['monitoramento', 'cliente'])
                                   ->whereIn('id', $postsIds)
                                   ->get();
        
        $noticiasIds = [];
        
        foreach ($posts as $post) {
            // Verificar se já existe uma notícia para este post
            $noticiaExistente = self::where('post_coleta_id', $post->id)->first();
            if ($noticiaExistente) {
                continue; // Pula se já existe
            }
            
            // Criar a notícia com limpeza UTF-8
            $noticia = self::create([
                'post_coleta_id' => $post->id,
                'titulo' => self::limparTexto(self::gerarTitulo($post)),
                'resumo' => self::limparTexto($post->texto),
                'rede_social' => self::limparTexto($post->tipo_midia),
                'autor_nome' => self::limparTexto($post->autor_nome),
                'autor_username' => self::limparTexto($post->autor_username),
                'data_publicacao' => $post->data_publicacao,
                'url_post' => self::limparTexto($post->url_post),
                'metricas_engagement' => [
                    'likes' => $post->likes ?? 0,
                    'shares' => $post->shares ?? 0,
                    'comentarios' => $post->comentarios ?? 0,
                    'views' => $post->views ?? 0
                ],
                'sentimento_inicial' => self::avaliarSentimento($post),
                'valor_retorno' => self::calcularValorRetorno($post),
                'cd_usuario' => Auth::id(),
                'misc_data' => [
                    'hashtags' => $post->hashtags,
                    'mencoes' => $post->mencoes,
                    'tem_imagem' => $post->tem_imagem,
                    'tem_video' => $post->tem_video,
                    'urls_midia' => $post->urls_midia,
                    'relevancia_score' => $post->relevancia_score,
                    'post_original_data' => $post->misc_data
                ]
            ]);
            
            // Vincular ao cliente através da tabela noticia_cliente
            if ($post->cliente_id && $noticia) {
                \App\Models\NoticiaCliente::create([
                    'cliente_id' => $post->cliente_id,
                    'tipo_id' => 5, // Mídias Sociais
                    'noticia_id' => $noticia->id,
                    'sentimento' => self::avaliarSentimento($post),
                    'monitoramento_id' => $post->monitoramento_id,
                    'misc_data' => [
                        'rede_social' => $post->tipo_midia,
                        'data_criacao_noticia' => now(),
                        'post_original_id' => $post->id
                    ]
                ]);
            }
            
            // Marcar post como processado
            $post->update(['processado' => true]);
            
            $noticiasIds[] = $noticia->id;
        }
        
        return $noticiasIds;
    }

    // Métodos auxiliares
    private static function limparTexto($texto)
    {
        if (!$texto) {
            return null;
        }
        
        // Converter para UTF-8 e remover caracteres problemáticos
        $texto = mb_convert_encoding($texto, 'UTF-8', 'UTF-8');
        
        // Remover caracteres de controle não imprimíveis
        $texto = preg_replace('/[\x00-\x1F\x7F]/u', '', $texto);
        
        // Limpar quebras de linha extras
        $texto = preg_replace('/[\r\n]+/', ' ', $texto);
        
        return trim($texto);
    }
    
    private static function gerarTitulo($post)
    {
        $titulo = $post->texto;
        
        // Limitar a 100 caracteres
        if (strlen($titulo) > 100) {
            $titulo = substr($titulo, 0, 97) . '...';
        }
        
        // Se não tem texto, usar dados do autor
        if (empty($titulo)) {
            $titulo = "Post de " . ($post->autor_display ?? 'usuário não identificado');
        }
        
        return $titulo;
    }
    
    private static function avaliarSentimento($post)
    {
        // Lógica simples baseada em palavras-chave
        $texto = strtolower($post->texto ?? '');
        
        $palavrasPositivas = ['ótimo', 'excelente', 'maravilhoso', 'perfeito', 'adorei', 'amei', 'incrível', 'fantástico'];
        $palavrasNegativas = ['ruim', 'péssimo', 'horrível', 'terrível', 'odiei', 'decepcionante', 'frustrante'];
        
        $contadorPositivo = 0;
        $contadorNegativo = 0;
        
        foreach ($palavrasPositivas as $palavra) {
            if (strpos($texto, $palavra) !== false) {
                $contadorPositivo++;
            }
        }
        
        foreach ($palavrasNegativas as $palavra) {
            if (strpos($texto, $palavra) !== false) {
                $contadorNegativo++;
            }
        }
        
        if ($contadorPositivo > $contadorNegativo) {
            return 1; // Positivo
        } elseif ($contadorNegativo > $contadorPositivo) {
            return -1; // Negativo
        }
        
        return 0; // Neutro
    }
    
    private static function calcularValorRetorno($post)
    {
        $valor = 0;
        
        // Valores base por engajamento
        $valor += ($post->likes ?? 0) * 0.05;
        $valor += ($post->shares ?? 0) * 0.25;
        $valor += ($post->comentarios ?? 0) * 0.50;
        $valor += ($post->views ?? 0) * 0.001;
        
        // Bonus por mídia
        if ($post->tem_imagem) {
            $valor *= 1.2;
        }
        if ($post->tem_video) {
            $valor *= 1.5;
        }
        
        // Bonus por relevância
        if ($post->relevancia_score && $post->relevancia_score > 0.7) {
            $valor *= 1.3;
        }
        
        return round($valor, 2);
    }
}