<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PautaNoticia extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'pauta_noticia';

    protected $fillable = ['tipo_id','noticia_id','pauta_id'];

}