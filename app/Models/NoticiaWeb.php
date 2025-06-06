<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NoticiaWeb extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'noticias_web';

    protected $fillable = ['id_fonte',
                            'data_insert',
                            'data_noticia',
                            'titulo_noticia',
                            'url_noticia',
                            'nu_valor',
                            'fl_boletim',
                            'cd_usuario',
                            'sinopse',
                            'ds_caminho_img'];

    public function usuario()
    {
        return $this->hasOne(User::class, 'id', 'cd_usuario');
    }

    public function fonte()
    {
        return $this->hasOne(FonteWeb::class, 'id', 'id_fonte');
    }

    public function conteudo()
    {
        return $this->hasOne(ConteudoNoticiaWeb::class, 'id_noticia_web', 'id');
    }

    public function logs()
    {
        return $this->hasMany(LogAcesso::class, 'id_noticia', 'id')->where('tipo','web');
    }

    public function clientes()
    {
        return $this->belongsToMany(Cliente::class,'noticia_cliente','noticia_id','cliente_id')->withPivot('id','tipo_id','sentimento','area','fl_enviada')->where('tipo_id', 2)->withTimestamps();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class,'noticia_tag','noticia_id','tag_id')->withPivot('tipo_id')->where('tipo_id', 2)->withTimestamps();
    }

    protected static function booted () {
        static::deleting(function(NoticiaWeb $noticia) { 
            $noticia->conteudo()->delete();
        });
    }  
}