<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NoticiaImpresso extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'noticia_impresso';

    protected $fillable = ['id_fonte','id_sessao_impresso','dt_clipagem','titulo','texto','sinopse','nu_paginas_total','nu_pagina_atual','ds_caminho_img','nu_colunas','nu_altura', 'nu_largura','valor_retorno'];

    public function fonte()
    {
        return $this->hasOne(FonteImpressa::class, 'id', 'id_fonte');
    }
}