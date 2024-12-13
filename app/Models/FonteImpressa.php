<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FonteImpressa extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'jornal_online';

    protected $fillable = ['codigo', 'nome','cd_estado', 'cd_cidade', 'tipo', 'coleta','modelo', 'url', 'with_login','retorno_midia'];

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'cd_estado', 'cd_estado');
    }

    public function cidade()
    {
        return $this->belongsTo(Cidade::class, 'cd_cidade', 'cd_cidade');
    }

    public function tipoImpresso()
    {
        return $this->hasOne(TipoImpresso::class, 'id', 'tipo');
    }

    public function tipoColeta()
    {
        return $this->hasOne(TipoColeta::class, 'id', 'coleta');
    }

    public function edicoes()
    {
        return $this->hasMany(EdicaoJornalImpresso::class);
    }

    public function secoes()
    {
        return $this->hasMany(SecaoImpresso::class,'id_jornal_online','id');
    }
}