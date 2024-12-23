<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramaEmissoraWeb extends Model
{    
    protected $connection = 'pgsql';
    protected $table = 'programa_emissora_web';

    protected $fillable = [''];   
    
    public function emissora()
    {
        return $this->hasOne(EmissoraWeb::class, 'id', 'id_emissora');
    }
}