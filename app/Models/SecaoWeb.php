<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SecaoWeb extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'sessao_web';
    protected $primaryKey = 'id_sessao_web';

    protected $fillable = ['id_fonte_web','ds_sessao'];

}