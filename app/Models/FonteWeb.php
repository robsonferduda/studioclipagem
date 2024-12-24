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
                WHERE t1.deleted_at IS NULL
                GROUP BY id_situacao, ds_situacao, ds_color 
                ORDER BY ds_situacao";

        return DB::select($sql);
    }

    public function getColetasByFonte($id_fonte, $dt_inicial,$dt_final)
    {
        $sql = "SELECT data_insert::date, count(*) as total   
                FROM noticias_web 
                where data_insert between '2024-12-11' AND '2024-12-18'
                AND id_fonte = 2
                GROUP BY data_insert::date";

        return DB::select($sql);
    }

    public function getTopColetas($n)
    {
        $data_inicio = date("Y-m-d")." 00:00:00";
        $data_fim = date("Y-m-d")." 23:59:59";

        $sql = "SELECT t2.id, t2.nome, t2.url, count(*) as total 
                FROM noticias_web t1
                JOIN fonte_web t2 ON t2.id = t1.id_fonte 
                WHERE data_insert between '$data_inicio' AND '$data_fim'
                GROUP BY t2.id, t2.nome, t2.url  
                ORDER BY total DESC";

            if($n > 0) $sql .= " LIMIT $n";

        return DB::select($sql);
    }

    public function getSemColetas($n)
    {
        $data_inicio = date("Y-m-d")." 00:00:00";
        $data_fim = date("Y-m-d")." 23:59:59";

        $sql = "SELECT t1.id, t1.nome, t1.url, count(t2.id) as total 
                FROM fonte_web t1
                LEFT JOIN noticias_web t2 ON t2.id_fonte = t1.id AND data_insert between '$data_inicio' AND '$data_fim'
                GROUP BY t1.id, t1.nome, t1.url
                HAVING count(t2.id) = 0";

            if($n > 0) $sql .= " LIMIT $n";

        return DB::select($sql);
    }
}