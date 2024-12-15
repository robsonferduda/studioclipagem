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

    public function getNoticiaByFonte($id_fonte, $data)
    {
        $sql = "SELECT * FROM app_web WHERE data_cadastro > '$data' AND veiculoid = $id_fonte ORDER BY data_cadastro DESC LIMIT 1";

        return DB::connection('mysql')->select($sql);
    }

    public function getFontes($d1, $d2)
    {
        $sql = "SELECT t2.titulo, t2.dominio, t2.id_knewin, count(*) 
                FROM app_web t1
                JOIN app_importacaoveiculos t2 ON t2.id_knewin = t1.veiculoid 
                WHERE t1.data_clipping BETWEEN '$d1' AND '$d2'
                GROUP BY t2.titulo, t2.dominio, t2.id_knewin";
        
        return DB::connection('mysql')->select($sql);
    }

    public function getEstado($estado)
    {
        $sql = "SELECT * FROM app_importacaoveiculos where id_knewin = '$estado'";
        
        return DB::connection('mysql')->select($sql);
    }

    public function getValor($fonte)
    {
        $sql = "SELECT * FROM app_web_sites WHERE id_knewin = $fonte";
        
        return DB::connection('mysql')->select($sql);
    }
}