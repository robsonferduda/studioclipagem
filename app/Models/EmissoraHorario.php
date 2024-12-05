<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmissoraHorario extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'horarios_emissora_radio';

    protected $fillable = [];
}