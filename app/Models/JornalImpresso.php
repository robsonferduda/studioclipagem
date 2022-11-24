<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JornalImpresso extends Model
{    
    protected $connection = 'pgsql';
    protected $table = 'noticia_impresso';

    protected $fillable = [''];       
}