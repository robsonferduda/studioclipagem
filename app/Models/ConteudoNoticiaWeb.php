<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConteudoNoticiaWeb extends Model
{
    use SoftDeletes;
    use Searchable;

    protected $connection = 'pgsql';
    protected $table = 'conteudo_noticia_web';

    protected $fillable = ['id_noticia_web','conteudo'];

    public function getScoutKey(): string
    {
        return $this->conteudo;
    }

    public function getScoutKeyName(): string
    {
        return 'conteudo';
    }

}