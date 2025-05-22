<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmissoraGravacao extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'gravacao_emissora_radio';

    protected $fillable = [];  

    public function emissora()
    {
        return $this->hasOne(Emissora::class, 'id', 'id_emissora');
    }
}