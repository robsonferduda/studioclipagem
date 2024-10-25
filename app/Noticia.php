<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class Noticia extends Model
{
    const UPDATED_AT = null;
    
    protected $connection = 'mysql';
    protected $table = 'app_web';

    protected $fillable = ['status_envio'];   
    
    public function getNoticias($id_fonte)
    {
        $sql = "SELECT * FROM app_web WHERE data_cadastro > '2023-11-01' AND veiculoid = $id_fonte ORDER BY data_cadastro DESC LIMIT 10";

        return DB::connection('mysql')->select($sql);
    }

    public function getNoticiaByFonte($id_fonte)
    {
        $sql = "SELECT * FROM app_web WHERE data_cadastro > '2023-01-01' AND veiculoid = $id_fonte ORDER BY data_cadastro DESC LIMIT 1";

        return DB::connection('mysql')->select($sql);
    }
}