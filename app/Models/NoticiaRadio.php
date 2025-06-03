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
        'emissora_id',
        'cd_estado',
        'cd_cidade',
        'titulo',
        'arquivo',
        'programa_id',
        'dt_cadastro',
        'dt_clipagem',
        'fl_boletim',
        'sinopse',
        'valor_retorno',
        'link',
        'duracao',
        'horario',
        'cd_usuario',
        'ds_caminho_audio'
    ];

    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'id', 'cliente_id');
    }

    public function clientes()
    {
        return $this->belongsToMany(Cliente::class,'noticia_cliente','noticia_id','cliente_id')->withTimestamps()->whereNull('noticia_cliente.deleted_at');
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
        return $this->belongsToMany(Tag::class,'noticia_tag','noticia_id','tag_id')->withPivot('tipo_id')->where('tipo_id', 3)->withTimestamps();
    }

    public function getTotais($dt_inicial, $dt_final)
    {
        $sql = "SELECT dt_noticia, count(*) AS total FROM noticia_radio WHERE deleted_at IS NULL AND created_at BETWEEN '$dt_inicial' AND '$dt_final' GROUP BY dt_noticia ORDER BY dt_noticia";

        return DB::select($sql);
    }

    protected static function booted () {
        static::deleting(function(NoticiaRadio $noticia) { 
            $noticia->tags()->delete();
        });
    } 
}