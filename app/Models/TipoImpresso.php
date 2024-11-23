<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoImpresso extends Model
{    
    protected $connection = 'pgsql';
    protected $table = 'tipo_impresso';

    protected $fillable = [''];     
}