<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{    
    protected $connection = 'pgsql';
    protected $table = 'monitoramento_periodo';

    protected $fillable = [''];   
    
}