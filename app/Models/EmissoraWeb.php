<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmissoraWeb extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'emissora_web';

    protected $fillable = ['cd_pais',
                           'cd_estado',
                           'cd_cidade',
                           'nome_emissora',
                           'url_stream',
                           'id_situacao',
                           'valor',
                           'gravar'];

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
        return $this->hasMany(ProgramaEmissoraWeb::class, 'id_emissora', 'id');
    }

    public function horarios()
    {
        return $this->hasMany(EmissoraWebHorario::class, 'id_emissora', 'id');
    }
}