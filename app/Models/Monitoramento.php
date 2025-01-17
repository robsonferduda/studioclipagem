<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Monitoramento extends Model
{    
    protected $connection = 'pgsql';
    protected $table = 'monitoramento';

    protected $fillable = ['id_cliente','expressao','frequencia','fl_impresso','fl_radio','fl_web','fl_tv','nome','updated_at','hora_inicio','hora_fim','dt_inicio','dt_fim'];   
    
    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'id', 'id_cliente');
    }

    public function tipo()
    {
        return $this->hasOne(TipoFonte::class, 'id', 'tipo_midia');
    }

    public function fontes()
    {
        return $this->hasMany(FontWeb::class, 'id', 'id_fila');
    }

    public function noticias()
    {
        return $this->hasMany(NoticiaCliente::class, 'monitoramento_id', 'id');
    }

    public function historico()
    {
        return $this->hasMany(MonitoramentoExecucao::class, 'monitoramento_id', 'id')->orderBy('created_at','DESC');
    }
}