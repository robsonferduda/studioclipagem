<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fonte extends Model
{    
    protected $connection = 'pgsql';
    protected $table = 'fonte';

    protected $fillable = [''];       
}