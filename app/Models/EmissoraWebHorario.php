<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmissoraWebHorario extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'horarios_programa_emissora_web';

    protected $fillable = ['id_programa','horario_start','horario_end','dias_da_semana'];
}