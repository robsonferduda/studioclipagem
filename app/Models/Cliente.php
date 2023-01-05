<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{    
    protected $connection = 'pgsql';
    protected $table = 'pessoas';

    protected $fillable = [''];     
    
    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'id', 'id');
    }
}