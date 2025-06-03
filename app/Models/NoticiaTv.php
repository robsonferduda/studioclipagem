<?php

namespace App\Models;

use DB;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NoticiaTv extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'noticia_tv';

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
        'dt_cadastro',
        'fl_boletim',
        'sinopse',
        'link',
        'sentimento',
        'valor_retorno',
        'duracao',
        'decupagem_id',
        'cd_usuario',
        'ds_caminho_video'
    ];

    public function usuario()
    {
        return $this->hasOne(User::class, 'id', 'cd_usuario');
    }

    public function cliente()
    {
        return $this->hasOne(NoticiaCliente::class, 'id', 'cliente_id');
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
        return $this->hasOne(EmissoraWeb::class, 'id', 'emissora_id');
    }

    public function programa()
    {
        return $this->hasOne(ProgramaEmissoraWeb::class, 'id', 'programa_id');
    }

    public function clientes()
    {
        return $this->belongsToMany(Cliente::class,'noticia_cliente','noticia_id','cliente_id')->withPivot('tipo_id','sentimento','area')->where('tipo_id', 4)->withTimestamps();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class,'noticia_tag','noticia_id','tag_id')->withPivot('tipo_id')->where('tipo_id', 4)->withTimestamps();
    }

    public function getTotais()
    {
        $sql = "SELECT dt_noticia, count(*) AS total FROM noticia_tv WHERE deleted_at IS NULL GROUP BY dt_noticia ORDER BY dt_noticia";

        return DB::select($sql);
    }
 
}