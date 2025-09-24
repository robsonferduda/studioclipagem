<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MidiasSocialMonitoramento extends Model
{
    protected $table = 'midias_social_monitoramento';
    
    protected $fillable = [
        'cliente_id',
        'nome',
        'descricao',
        'tipo_midia',
        'palavras_chave',
        'status',
        'configuracoes',
        'misc_data',
        'ultima_coleta'
    ];

    protected $casts = [
        'configuracoes' => 'array',
        'misc_data' => 'array',
        'data_criacao' => 'datetime',
        'data_atualizacao' => 'datetime',
        'ultima_coleta' => 'datetime'
    ];

    const CREATED_AT = 'data_criacao';
    const UPDATED_AT = 'data_atualizacao';

    // Relacionamentos
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function coletas()
    {
        return $this->hasMany(MidiasSociaisColeta::class, 'monitoramento_id');
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('status', 'ativo');
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_midia', $tipo);
    }

    // Accessors
    public function getTipoMidiaArrayAttribute()
    {
        // Se tipo_midia é uma string separada por vírgula, converte para array
        if (is_string($this->tipo_midia)) {
            return explode(',', $this->tipo_midia);
        }
        return (array) $this->tipo_midia;
    }

    public function getPostsCountAttribute()
    {
        return $this->coletas()->count();
    }

    public function getPostsHojeCountAttribute()
    {
        return $this->coletas()->whereDate('data_coleta', today())->count();
    }

    // Mutators para array PostgreSQL
    public function setPalavrasChaveAttribute($value)
    {
        if (is_array($value)) {
            // Converter array PHP para formato PostgreSQL
            $this->attributes['palavras_chave'] = '{' . implode(',', array_map(function($item) {
                return '"' . str_replace('"', '\"', $item) . '"';
            }, $value)) . '}';
        } else {
            $this->attributes['palavras_chave'] = $value;
        }
    }

    public function getPalavrasChaveAttribute($value)
    {
        if (is_string($value) && strpos($value, '{') === 0) {
            // Converter formato PostgreSQL para array PHP
            $clean = trim($value, '{}');
            if (empty($clean)) {
                return [];
            }
            return array_map(function($item) {
                return trim(str_replace('\"', '"', $item), '"');
            }, explode(',', $clean));
        }
        return is_array($value) ? $value : [];
    }
}
