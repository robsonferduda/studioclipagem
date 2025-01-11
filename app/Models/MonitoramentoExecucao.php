<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoramentoExecucao extends Model
{    
    protected $connection = 'pgsql';
    protected $table = 'monitoramento_execucao';

    protected $fillable = ['monitoramento_id','total_vinculado','fl_automatico','id_user','created_at','updated_at'];
    
    public function monitoramento()
    {
        return $this->hasOne(Monitoramento::class, 'id', 'monitoramento_id');
    }

    public function usuario()
    {
        return $this->hasOne('App\User', 'id', 'id_user');
    }    
}