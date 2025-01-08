<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EdicaoJornalImpresso extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'edicao_jornal_online';

    protected $fillable = ['path_s3', 'dt_coleta', 'id_jornal_online', 'dt_pub', 'titulo','link_pdf','fl_upload'];

    public function fonte()
    {
        return $this->belongsTo(FonteImpressa::class, 'id_jornal_online');
    }

    public function paginas()
    {
        return $this->hasMany(PaginaJornalImpresso::class, 'id_edicao_jornal_online','id')->orderBy('n_pagina','ASC');
    }

    public function primeiraPagina()
    {
        return $this->hasOne(PaginaJornalImpresso::class, 'id_edicao_jornal_online','id')->where('n_pagina',1);
    }
}