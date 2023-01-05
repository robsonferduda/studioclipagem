<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pessoa extends Model
{    
    protected $connection = 'pgsql';
    protected $table = 'pessoas';

    protected $fillable = [''];     
    
    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'id', 'id');
    }
}