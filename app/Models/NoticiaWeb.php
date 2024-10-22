<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NoticiaWeb extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'noticias_web';

    protected $fillable = [];

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
}