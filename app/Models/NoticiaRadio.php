<?php

namespace App\Models;

use DB;
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

    public function tags()
    {
        return $this->belongsToMany(Tag::class,'tag_radio','noticia_id','tag_id')->withPivot('tipo_id')->withTimestamps();
    }

    public function getTotais()
    {
        $sql = "SELECT dt_noticia, count(*) AS total FROM noticia_radio WHERE deleted_at IS NULL GROUP BY dt_noticia ORDER BY dt_noticia";

        return DB::select($sql);
    }
 
}