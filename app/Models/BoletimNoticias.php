<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BoletimNoticias extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'boletim_noticia';

    protected $fillable = ['id_cliente'];
    
}