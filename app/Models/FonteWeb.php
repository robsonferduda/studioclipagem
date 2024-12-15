<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FonteWeb extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'fonte_web';

    protected $fillable = ['codigo', 'id_knewin', 'nome', 'url', 'fl_coleta', 'cd_cidade', 'cd_estado', 'cd_pais', 'id_situacao','id_prioridade','nu_valor'];

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'cd_estado', 'cd_estado');
    }

    public function cidade()
    {
        return $this->hasOne(Cidade::class, 'cd_cidade', 'cd_cidade');
    }  

    public function situacao()
    {
        return $this->hasOne(SituacaoFonteWeb::class, 'id_situacao_fonte_web', 'id_situacao');
    }  

    public function prioridade()
    {
        return $this->hasOne(Prioridade::class, 'id', 'id_prioridade');
    }  
    
    public function noticias()
    {
        return $this->hasMany(NoticiaWeb::class, 'id_fonte', 'id');
    }

    protected static function booted () {
        static::deleting(function(FonteWeb $fonte) { 
            $fonte->noticias()->delete();
        });
    }

    public function getSituacoes()
    {
        $sql = "SELECT id_situacao, ds_situacao, ds_color, count(*) AS total  
                FROM fonte_web t1
                LEFT JOIN situacao_fonte_web t2 ON t2.id_situacao_fonte_web = t1.id_situacao 
                WHERE id_situacao NOT IN(127,112,103,137)
                GROUP BY id_situacao, ds_situacao, ds_color 
                ORDER BY ds_situacao";

        return DB::select($sql);
    }

    public function getColetasByFonte($id_fonte, $dt_inicial,$dt_final)
    {
        $sql = "SELECT t1.id,t3.nome, count(*) as total   
                FROM noticia_cliente t1
                JOIN noticias_web t2 ON t2.id = t1.noticia_id AND tipo_id = 2
                JOIN clientes t3 ON t3.id = t1.cliente_id 
                WHERE t2.id_fonte = 674
                AND t2.data_insert between '2024-01-01 00:00:00' AND '2024-12-01 00:00:00'
                GROUP BY t1.id";

        return DB::select($sql);
    }

    public function getTopColetas()
    {
        $data_inicio = date("Y-m-d")." 00:00:00";
        $data_fim = date("Y-m-d")." 23:59:59";

        $sql = "SELECT t1.id, t1.nome, t1.url, count(t2.id) as total 
                FROM fonte_web t1
                LEFT JOIN noticias_web t2 ON t2.id_fonte = t1.id AND data_insert between '$data_inicio' AND '$data_fim'
                GROUP BY t1.id, t1.nome, t1.url
                ORDER BY total DESC";

        return DB::select($sql);
    }

    public function getSemColetas()
    {
        $data_inicio = date("Y-m-d")." 00:00:00";
        $data_fim = date("Y-m-d")." 23:59:59";

        $sql = "SELECT t1.id, t1.nome, t1.url, count(t2.id) as total 
                FROM fonte_web t1
                LEFT JOIN noticias_web t2 ON t2.id_fonte = t1.id AND data_insert between '$data_inicio' AND '$data_fim'
                GROUP BY t1.id, t1.nome, t1.url
                HAVING count(t2.id) = 0";

        return DB::select($sql);
    }
}