<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConteudoNoticiaWeb extends Model
{
    use SoftDeletes;
   
    protected $connection = 'pgsql';
    protected $table = 'conteudo_noticia_web';
    protected $fillable = ['id_noticia_web','conteudo'];

    public function noticia()
    {
        return $this->belongsTo(NoticiaWeb::class, 'id_noticia_web', 'id');
    }
}