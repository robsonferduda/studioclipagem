<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostInstagram extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'post_instagram';

    protected $fillable = [''];

    public function clientes()
    {
        return $this->belongsToMany(Cliente::class,'noticia_cliente','noticia_id','cliente_id')->withPivot('id','tipo_id','sentimento','area')->where('tipo_id', 6)->withTimestamps();
    }
}