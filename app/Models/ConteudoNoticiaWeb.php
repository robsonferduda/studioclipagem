<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConteudoNoticiaWeb extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'conteudo_noticia_web';

    protected $fillable = [];

}