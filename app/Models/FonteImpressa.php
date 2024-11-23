<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FonteImpressa extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'jornal_online';

    protected $fillable = ['codigo', 'nome', 'cd_cidade', 'tipo', 'url', 'with_login'];

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'cd_estado', 'cd_estado');
    }

    public function cidade()
    {
        return $this->belongsTo(Cidade::class, 'cd_cidade', 'cd_cidade');
    }

    public function tipos()
    {
        return $this->hasOne(TipoImpresso::class, 'id', 'tipo');
    }

    public function edicoes()
    {
        return $this->hasMany(EdicaoJornalImpresso::class);
    }
}