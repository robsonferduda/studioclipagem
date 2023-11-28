<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class FonteWeb extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'fonte_web';

    protected $fillable = ['codigo', 'nome', 'url', 'fl_coleta', 'cd_cidade', 'cd_estado'];

    public $timestamps = false;

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'cd_estado', 'cd_estado');
    }

    public function cidade()
    {
        return $this->hasOne(Cidade::class, 'cd_cidade', 'cd_cidade');
    }  

    public function getSituacoes()
    {
        $sql = "SELECT id_situacao, ds_situacao, ds_color, count(*) AS total  
                FROM fonte_web t1
                LEFT JOIN situacao_fonte_web t2 ON t2.id_situacao_fonte_web = t1.id_situacao 
                GROUP BY id_situacao, ds_situacao, ds_color 
                ORDER BY ds_situacao";

        return DB::select($sql);
    }

    public function getColetasByFonte($id_fonte, $dt_inicial,$dt_final)
    {
        $sql = "SELECT t4.id, t4.nome, t3.logo, count(*) as total   
                FROM noticia_cliente t1
                JOIN noticia_web t2 ON t2.id = t1 .noticia_id AND tipo_id = 2
                JOIN clientes t3 ON t3.id = t1.cliente_id 
                JOIN pessoas t4 ON t4.id = t3.pessoa_id 
                WHERE t2.id_fonte = $id_fonte
                AND t2.dt_clipagem between '$dt_inicial' AND '$dt_final'
                GROUP BY t4.id, t4.nome, t3.logo";

        return DB::select($sql);
    }

    public function getTopColetas()
    {
        $data_inicio = date("Y-m-d")." 00:00:00";
        $data_fim = date("Y-m-d")." 23:59:59";

        $sql = "SELECT t2.id, t2.nome, t2.url, count(*) AS total
                FROM noticia_web t1,
                    fonte_web t2
                WHERE t1.id_fonte = t2.id 
                AND dt_clipagem BETWEEN '$data_inicio' AND '$data_fim'
                GROUP BY t2.id, t2.nome, t2.url
                ORDER BY total DESC
                LIMIT 10";

        return DB::select($sql);
    }

    public function getSemColetas()
    {
        $data_inicio = date("Y-m-d")." 00:00:00";
        $data_fim = date("Y-m-d")." 23:59:59";

        $sql = "SELECT * 
                FROM fonte_web 
                WHERE id NOT IN (SELECT DISTINCT id_fonte FROM noticia_web WHERE dt_clipagem BETWEEN '$data_inicio' AND '$data_fim')";

        return DB::select($sql);
    }
}