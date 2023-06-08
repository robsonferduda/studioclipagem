<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NoticiaRadio extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'noticia_radio';

    protected $fillable = [
        'cliente_id',
        'area_id',
        'emissora_id',
        'cd_estado',
        'cd_cidade',
        'titulo',
        'arquivo',
        'programa_id',
        'dt_noticia',
        'fl_boletim',
        'sinopse',
        'link',
        'sentimento',
        'duracao'
    ];

    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'id', 'cliente_id');
    }

    public function area()
    {
        return $this->hasOne(Area::class, 'id', 'area_id');
    }

    public function estado()
    {
        return $this->hasOne(Estado::class, 'cd_estado', 'cd_estado');
    }

    public function cidade()
    {
        return $this->hasOne(Cidade::class, 'cd_cidade', 'cd_cidade');
    }

    public function emissora()
    {
        return $this->hasOne(Emissora::class, 'id', 'emissora_id');
    }

    public function programa()
    {
        return $this->hasOne(Programa::class, 'id', 'programa_id');
    }
}
