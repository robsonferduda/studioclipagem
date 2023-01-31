<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FonteWeb extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'fonte_web';

    protected $fillable = ['url', 'nome', 'fl_coleta', 'cod_unico', 'cd_cidade', 'cd_estado'];

    public $timestamps = false;

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'cd_estado', 'cd_estado');
    }

    public function cidade()
    {
        return $this->belongsTo(Cidade::class, 'cd_cidade', 'cd_cidade');
    }
}