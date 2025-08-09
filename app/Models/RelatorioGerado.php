<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RelatorioGerado extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'relatorios';

    protected $fillable = [
        'id_tipo',
        'ds_nome',
        'cd_usuario',
        'dt_requisicao',
        'dt_finalizacao',
        'total_view',
        'situacao',
        'misc_data'
    ];

    protected $casts = [
        'misc_data' => 'array',
        'dt_requisicao' => 'datetime',
        'dt_finalizacao' => 'datetime'
    ];

    public function usuario()
    {
        return $this->hasOne(User::class, 'id', 'cd_usuario');
    }

    /**
     * Cria um registro de relatório gerado com os dados do PDF
     */
    public static function criarRelatorioGerado($dadosRelatorio)
    {
        return self::create([
            'id_tipo' => 1, // Tipo de relatório (PDF)
            'ds_nome' => $dadosRelatorio['nome_arquivo'] ?? 'relatorio.pdf',
            'cd_usuario' => auth()->id(),
            'dt_requisicao' => now(),
            'dt_finalizacao' => now(),
            'situacao' => 1, // 1 = concluído
            'total_view' => 0,
            'misc_data' => [
                'tipo' => 'relatorio_pdf',
                'titulo' => $dadosRelatorio['titulo'] ?? 'Relatório PDF Gerado',
                'descricao' => $dadosRelatorio['descricao'] ?? 'Relatório PDF gerado pelo sistema',
                'arquivo_nome' => $dadosRelatorio['nome_arquivo'] ?? null,
                'url_s3' => $dadosRelatorio['url_s3'] ?? null,
                'data_inicio' => $dadosRelatorio['data_inicio'] ?? null,
                'data_fim' => $dadosRelatorio['data_fim'] ?? null,
                'cliente_id' => $dadosRelatorio['cliente_id'] ?? null,
                'filtros' => $dadosRelatorio['filtros'] ?? [],
                'tamanho_arquivo' => $dadosRelatorio['tamanho_arquivo'] ?? null,
                'total_noticias' => $dadosRelatorio['total_noticias'] ?? null,
                'tipos_midia' => $dadosRelatorio['tipos_midia'] ?? [],
                'valor_total_retorno' => $dadosRelatorio['valor_total_retorno'] ?? 0,
                'gerado_em' => now()->toISOString()
            ]
        ]);
    }

    /**
     * Accessor para obter o título
     */
    public function getTituloAttribute()
    {
        return $this->misc_data['titulo'] ?? $this->ds_nome ?? 'Relatório';
    }

    /**
     * Accessor para obter a URL pública do S3
     */
    public function getUrlS3Attribute()
    {
        return $this->misc_data['url_s3'] ?? null;
    }

    /**
     * Accessor para obter o nome do arquivo
     */
    public function getNomeArquivoAttribute()
    {
        return $this->misc_data['arquivo_nome'] ?? null;
    }

    /**
     * Accessor para obter os filtros aplicados
     */
    public function getFiltrosAplicadosAttribute()
    {
        return $this->misc_data['filtros'] ?? [];
    }

    /**
     * Accessor para obter dados do cliente
     */
    public function getClienteIdAttribute()
    {
        return $this->misc_data['cliente_id'] ?? null;
    }

    /**
     * Scope para filtrar relatórios de um cliente específico
     */
    public function scopeDoCliente($query, $clienteId)
    {
        return $query->whereJsonContains('misc_data->cliente_id', $clienteId);
    }

    /**
     * Scope para filtrar relatórios por período
     */
    public function scopePorPeriodo($query, $dataInicio, $dataFim)
    {
        return $query->whereBetween('data_insert', [$dataInicio, $dataFim]);
    }
}
