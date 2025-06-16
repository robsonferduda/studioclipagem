<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NoticiaCliente extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'noticia_cliente';

    protected $fillable = ['cliente_id','tipo_id','noticia_id','monitoramento_id','sentimento','area','fl_boletim','fl_enviada','id_noticia_origem'];

    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'id', 'cliente_id');
    }

    public function noticiaWeb()
    {
        return $this->hasOne(NoticiaWeb::class, 'id', 'noticia_id');
    }

    public function noticiaImpressa()
    {
        return $this->hasOne(JornalImpresso::class, 'id', 'noticia_id');
    }

    public function monitoramento()
    {
        return $this->belongsTo(Monitoramento::class, 'monitoramento_id', 'id');
    }
}