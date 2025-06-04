<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BoletimEnvio extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'boletim_envio';

    protected $fillable = ['id_boletim',
                            'ds_email',
                            'id_situacao',
                            'ds_mensagem',
                            'cd_usuario'];

    public function usuario()
    {
        return $this->hasOne('App\User','id','cd_usuario');
    }
}