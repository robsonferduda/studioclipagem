<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cidade extends Model
{    
    protected $connection = 'pgsql';
    protected $table = 'cidade';

    protected $fillable = [''];   
    
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'cd_estado', 'cd_estado');
    }

    public function regional()
    {
        return $this->belongsTo(Regional::class, 'cd_regional', 'cd_regional');
    }    
}