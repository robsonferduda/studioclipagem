<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Boletim extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'boletim';

    protected $fillable = ['id_cliente','id_situacao','dt_boletim','titulo','total_views','id_usuario'];

    public function cliente()
    {
        return $this->hasOne('App\Models\Cliente','id','id_cliente');
    }

    public function situacao()
    {
        return $this->hasOne('App\Models\SituacaoBoletim','id','id_situacao');
    }

    public function usuario()
    {
        return $this->hasOne('App\User','id','id_usuario');
    }

    public function noticias()
    {
        return array();
    }
}