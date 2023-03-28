<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoticiaCliente extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'noticia_cliente';

    protected $fillable = ['cliente_id','tipo_id','noticia_id','monitoramento_id'];

    public function noticiaWeb()
    {
        return $this->hasOne(JornalWeb::class, 'id', 'noticia_id');
    }

    public function monitoramento()
    {
        return $this->belongsTo(Monitoramento::class, 'monitoramento_id', 'id');
    }
}