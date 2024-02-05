<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FonteImpressa extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'fonte_impressa';

    protected $fillable = ['codigo', 'nome', 'cd_cidade', 'cd_estado'];

    public function noticias()
    {
        return $this->belongsTo(JornalImpresso::class, 'id_fonte', 'id');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'cd_estado', 'cd_estado');
    }

    public function cidade()
    {
        return $this->belongsTo(Cidade::class, 'cd_cidade', 'cd_cidade');
    }
}