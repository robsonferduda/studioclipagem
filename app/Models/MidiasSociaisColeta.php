<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MidiasSociaisColeta extends Model
{
    protected $table = 'midias_sociais_coletas';
    
    protected $fillable = [
        'monitoramento_id',
        'cliente_id',
        'tipo_midia',
        'post_id',
        'autor_nome',
        'autor_username',
        'autor_id',
        'texto',
        'data_publicacao',
        'url_post',
        'likes',
        'shares',
        'comentarios',
        'views',
        'tem_imagem',
        'tem_video',
        'urls_midia',
        'idioma',
        'localizacao',
        'hashtags',
        'mencoes',
        'misc_data',
        'processado',
        'relevancia_score'
    ];

    protected $casts = [
        'misc_data' => 'array',
        'data_publicacao' => 'datetime',
        'data_coleta' => 'datetime',
        'tem_imagem' => 'boolean',
        'tem_video' => 'boolean',
        'processado' => 'boolean',
        'relevancia_score' => 'decimal:2'
    ];

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $dates = [
        'data_publicacao',
        'data_coleta'
    ];

    // Relacionamentos
    public function monitoramento()
    {
        return $this->belongsTo(MidiasSocialMonitoramento::class, 'monitoramento_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    // Scopes
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_midia', $tipo);
    }

    public function scopePorPeriodo($query, $dataInicial, $dataFinal)
    {
        return $query->whereBetween('data_publicacao', [$dataInicial, $dataFinal]);
    }

    public function scopeComImagem($query)
    {
        return $query->where('tem_imagem', true);
    }

    public function scopeComVideo($query)
    {
        return $query->where('tem_video', true);
    }

    public function scopeProcessados($query)
    {
        return $query->where('processado', true);
    }

    public function scopeRelevancia($query, $minimo)
    {
        return $query->where('relevancia_score', '>=', $minimo);
    }

    // Accessors for PostgreSQL arrays
    public function getUrlsMidiaAttribute($value)
    {
        if (!$value) return [];
        
        // Se já é array, retorna
        if (is_array($value)) return $value;
        
        // Converte array PostgreSQL para PHP array
        if (is_string($value)) {
            // Remove chaves { } 
            $cleaned = trim($value, '{}');
            if (empty($cleaned)) return [];
            
            // Para URLs, vamos procurar por padrões http/https
            preg_match_all('/https?:\/\/[^\s,}]+/', $cleaned, $matches);
            
            if (!empty($matches[0])) {
                return $matches[0];
            }
            
            // Fallback: dividir por vírgula simples
            return array_filter(array_map('trim', explode(',', $cleaned)));
        }
        
        return [];
    }
    
    public function getHashtagsAttribute($value)
    {
        if (!$value) return [];
        
        if (is_array($value)) return $value;
        
        if (is_string($value)) {
            $cleaned = trim($value, '{}');
            if (empty($cleaned)) return [];
            
            return array_filter(array_map('trim', explode(',', $cleaned)));
        }
        
        return [];
    }
    
    public function getMencoesAttribute($value)
    {
        if (!$value) return [];
        
        if (is_array($value)) return $value;
        
        if (is_string($value)) {
            $cleaned = trim($value, '{}');
            if (empty($cleaned)) return [];
            
            return array_filter(array_map('trim', explode(',', $cleaned)));
        }
        
        return [];
    }

    // Accessors
    public function getRelevanciaPercentualAttribute()
    {
        return $this->relevancia_score ? number_format($this->relevancia_score * 100, 0) : 0;
    }

    public function getAutorDisplayAttribute()
    {
        if ($this->autor_username) {
            return '@' . $this->autor_username;
        }
        return $this->autor_nome ?: 'Usuário não identificado';
    }

    public function getTemMidiaAttribute()
    {
        return $this->tem_imagem || $this->tem_video;
    }

    public function getTipoIconeAttribute()
    {
        $icones = [
            'twitter' => 'fa-twitter',
            'linkedin' => 'fa-linkedin',
            'facebook' => 'fa-facebook',
            'instagram' => 'fa-instagram'
        ];

        return $icones[$this->tipo_midia] ?? 'fa-share-alt';
    }

    public function getTipoCorAttribute()
    {
        $cores = [
            'twitter' => 'info',
            'linkedin' => 'primary',
            'facebook' => 'primary', 
            'instagram' => 'danger'
        ];

        return $cores[$this->tipo_midia] ?? 'secondary';
    }

    public function getProfilePicUrlAttribute()
    {
        if (!$this->misc_data) {
            return null;
        }
        
        // Se misc_data é string JSON, decodificar
        $data = $this->misc_data;
        if (is_string($data)) {
            $data = json_decode($data, true);
        }
        
        if (!is_array($data)) {
            return null;
        }

        switch ($this->tipo_midia) {
            case 'twitter':
                return $data['twitter_data']['user']['profile_pic_url'] ?? null;
                
            case 'facebook':
                return $data['user']['profile_pic_url'] ?? null;
                
            case 'instagram':
                return $data['user']['profile_pic_url'] ?? null;
                
            case 'linkedin':
                return $data['user']['profile_pic_url'] ?? null;
                
            default:
                return null;
        }
    }

    // Mutators
    public function setPalavrasChaveAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['palavras_chave'] = array_map('trim', explode(',', $value));
        } else {
            $this->attributes['palavras_chave'] = $value;
        }
    }
}
