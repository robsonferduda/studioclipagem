<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoFonte extends Model
{    
    protected $connection = 'pgsql';
    protected $table = 'tipo_fonte';

    protected $fillable = [''];     
}