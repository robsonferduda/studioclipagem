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


    public function getTopColetas()
    {
        $sql = "SELECT t2.id, t2.nome, t2.url, count(*) AS total
                FROM noticia_web t1,
                    fonte_web t2
                WHERE t1.id_fonte = t2.id 
                AND dt_clipagem >= '2021-04-04'
                GROUP BY t2.id, t2.nome, t2.url
                ORDER BY total DESC
                LIMIT 10";

        return DB::select($sql);
    }
}