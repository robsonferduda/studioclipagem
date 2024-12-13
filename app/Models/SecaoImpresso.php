<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SecaoImpresso extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'sessao_impresso';
    protected $primaryKey = 'id_sessao_impresso';

    protected $fillable = ['id_jornal_online','ds_sessao'];

}