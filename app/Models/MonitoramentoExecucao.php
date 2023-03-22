<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoramentoExecucao extends Model
{    
    protected $connection = 'pgsql';
    protected $table = 'monitoramento_execucao';

    protected $fillable = ['monitoramento_id','total_vinculado'];   
    
}