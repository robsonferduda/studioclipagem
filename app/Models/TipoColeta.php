<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoColeta extends Model
{    
    protected $connection = 'pgsql';
    protected $table = 'tipo_coleta';

    protected $fillable = [''];     
}