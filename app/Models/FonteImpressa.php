<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FonteImpressa extends Model
{    
    protected $connection = 'pgsql';
    protected $table = 'fonte_impressa';

    protected $fillable = ['cd_estado','cd_cidade','codigo','nome'];       
}