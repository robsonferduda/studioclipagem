<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formato extends Model
{    
    protected $connection = 'pgsql';
    protected $table = 'formato_impresso';

    protected $fillable = [''];   
}