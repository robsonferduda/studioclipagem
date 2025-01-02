<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramaEmissoraWeb extends Model
{    
    protected $connection = 'pgsql';
    protected $table = 'programa_emissora_web';

    protected $fillable = ['cd_pais','cd_estado','cd_cidade','nome_programa','url','id_emissora','tipo_programa','ip_local','gravar'];   

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'cd_estado', 'cd_estado');
    }

    public function cidade()
    {
        return $this->hasOne(Cidade::class, 'cd_cidade', 'cd_cidade');
    }  
    
    public function emissora()
    {
        return $this->hasOne(EmissoraWeb::class, 'id', 'id_emissora');
    }

    public function tipo()
    {
        return $this->hasOne(TipoProgramaEmissoraWeb::class, 'id', 'tipo_programa');
    }

    public function horarios()
    {
        return $this->hasMany(EmissoraWebHorario::class, 'id_programa', 'id');
    }
}