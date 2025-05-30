<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Boletim extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'boletim';

    protected $fillable = ['id_cliente','id_situacao','dt_boletim','titulo','total_views','id_usuario','dt_envio'];

    public function cliente()
    {
        return $this->hasOne('App\Models\Cliente','id','id_cliente');
    }

    public function situacao()
    {
        return $this->hasOne('App\Models\SituacaoBoletim','id','id_situacao');
    }

    public function usuario()
    {
        return $this->hasOne('App\User','id','id_usuario');
    }

    public function noticiasImpresso()
    {
        return $this->belongsToMany(NoticiaImpresso::class,'boletim_noticia','id_boletim','id_noticia')->withPivot('id_tipo')->where('id_tipo', 1)->withTimestamps();
    }

    public function noticiasRadio()
    {
        return $this->belongsToMany(NoticiaRadio::class,'boletim_noticia','id_boletim','id_noticia')->withPivot('id_tipo')->where('id_tipo', 3)->withTimestamps();
    }

    public function noticiasTv()
    {
        return $this->belongsToMany(NoticiaTv::class,'boletim_noticia','id_boletim','id_noticia')->withPivot('id_tipo')->where('id_tipo', 4)->withTimestamps();
    }

    public function noticiasWeb()
    {
        return $this->belongsToMany(NoticiaWeb::class,'boletim_noticia','id_boletim','id_noticia')->withPivot('id_tipo')->where('id_tipo', 2)->withTimestamps();
    }
}