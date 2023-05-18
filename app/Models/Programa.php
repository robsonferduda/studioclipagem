<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Programa extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'programa';

    protected $fillable = ['nome','emissora_id'];

    public function emissora()
    {
        return $this->hasOne(Emissora::class, 'id', 'emissora_id');
    }

    public function noticiaRadio()
    {
        return $this->hasMany(NoticiaRadio::class, 'id', 'programa_id');
    }

}
