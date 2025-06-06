<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Monitoramento extends Model implements Auditable
{    
    use SoftDeletes;
    use AuditableTrait;
    
    protected $connection = 'pgsql';
    protected $table = 'monitoramento';

    protected $fillable = ['id_cliente','expressao','frequencia','fl_impresso','fl_radio','fl_web','fl_tv','nome','updated_at','hora_inicio','hora_fim','dt_inicio','dt_fim','filtro_web','filtro_impresso','filtro_radio','filtro_tv'];     
    
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

    public function noticiasWeb()
    {
        return $this->hasMany(NoticiaCliente::class, 'monitoramento_id', 'id')->where("tipo_id",2);
    }

    public function noticiasImpresso()
    {
        return $this->hasMany(NoticiaCliente::class, 'monitoramento_id', 'id')->where("tipo_id",1);
    }

    public function noticiasRadio()
    {
        return $this->hasMany(NoticiaCliente::class, 'monitoramento_id', 'id')->where("tipo_id",3);
    }

    public function noticiasTv()
    {
        return $this->hasMany(NoticiaCliente::class, 'monitoramento_id', 'id')->where("tipo_id",4);
    }

    public function historico()
    {
        return $this->hasMany(MonitoramentoExecucao::class, 'monitoramento_id', 'id')->orderBy('created_at','DESC');
    }
}