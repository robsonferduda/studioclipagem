<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Emissora extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'emissora_radio';

    protected $fillable = ['cd_pais',
                            'cd_estado',
                            'cd_cidade', 
                            'nome_emissora', 
                            'url_stream', 
                            'gravar', 
                            'logo', 
                            'nu_valor',
                            'id_situacao'];

    public function estado()
    {
        return $this->hasOne(Estado::class, 'cd_estado', 'cd_estado');
    }

    public function cidade()
    {
        return $this->hasOne(Cidade::class, 'cd_cidade', 'cd_cidade');
    }

    public function programas()
    {
        return $this->hasMany(Programa::class, 'id_emissora', 'id');
    }

    public function horarios()
    {
        return $this->hasMany(EmissoraHorario::class, 'id_emissora', 'id');
    }
}
