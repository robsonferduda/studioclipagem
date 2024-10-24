<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Emissora extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'emissora';

    protected $fillable = ['ds_emissora','cd_estado','cd_cidade','codigo','fl_transicao','tipo_id'];

    public function estado()
    {
        return $this->hasOne(Estado::class, 'cd_estado', 'cd_estado');
    }

    public function cidade()
    {
        return $this->hasOne(Cidade::class, 'cd_cidade', 'cd_cidade');
    }

    public function noticiaRadio()
    {
        return $this->hasMany(NoticiaRadio::class, 'id', 'emissora_id');
    }
}
