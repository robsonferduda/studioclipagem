<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{    
    protected $connection = 'pgsql';
    protected $table = 'estado';

    protected $fillable = [''];     
    
    public function cidade()
    {
        return $this->hasMany(Cidade::class, 'cd_estado', 'cd_estado');
    }
}