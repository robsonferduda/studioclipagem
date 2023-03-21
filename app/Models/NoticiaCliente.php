<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoticiaCliente extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'noticia_cliente';

    protected $fillable = ['cliente_id','tipo_id','noticia_id'];
}