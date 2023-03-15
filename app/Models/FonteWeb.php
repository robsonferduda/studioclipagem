<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FonteWeb extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'fonte_web';

    protected $fillable = ['codigo', 'nome', 'url', 'fl_coleta', 'cd_cidade', 'cd_estado'];

    public $timestamps = false;

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'cd_estado', 'cd_estado');
    }

    public function cidade()
    {
        return $this->hasOne(Cidade::class, 'cd_cidade', 'cd_cidade');
    }  
}