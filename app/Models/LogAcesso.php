<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogAcesso extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'log_acesso_noticia';

    protected $fillable = ['tipo','usuario','id_noticia'];

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'usuario');
    }
}