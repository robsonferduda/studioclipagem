<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NoticiaImpresso extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'noticia_impresso';

    protected $fillable = ['id_fonte',
                            'id_sessao_impresso',
                            'cd_estado',
                            'cd_cidade',
                            'dt_clipagem',
                            'dt_cadastro',
                            'titulo',
                            'ds_link',
                            'texto',
                            'sinopse',
                            'nu_paginas_total',
                            'nu_pagina_atual',
                            'ds_caminho_img',
                            'nu_colunas',
                            'nu_altura', 
                            'nu_largura',
                            'local_impressao',
                            'cd_usuario',
                            'valor_retorno'];

    public function usuario()
    {
        return $this->hasOne(User::class, 'id', 'cd_usuario');
    }

    public function cidade()
    {
        return $this->hasOne(Cidade::class,'cd_cidade','cd_cidade');
    }

    public function estado()
    {
        return $this->hasOne(Estado::class,'cd_estado','cd_estado');
    }

    public function fonte()
    {
        return $this->hasOne(FonteImpressa::class, 'id', 'id_fonte');
    }

    public function secao()
    {
        return $this->hasOne(SecaoImpresso::class,'id_sessao_impresso','id_sessao_impresso');
    }

    public function clientes()
    {
        return $this->belongsToMany(Cliente::class,'noticia_cliente','noticia_id','cliente_id')->withPivot('tipo_id','sentimento','area')->where('tipo_id', 1)->withTimestamps();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class,'noticia_tag','noticia_id','tag_id')->withPivot('tipo_id')->where('tipo_id', 1)->withTimestamps();
    }
}