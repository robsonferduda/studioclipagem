<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Programa extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'programa_emissora_radio';

    protected $fillable = ['id_emissora','nome_programa','hora_inicio','hora_fim','valor_segundo','ordem'];

    public function emissora()
    {
        return $this->hasOne(Emissora::class, 'id', 'emissora_id');
    }

    public function noticiaRadio()
    {
        return $this->hasMany(NoticiaRadio::class, 'id', 'programa_id');
    }

}
