<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogBusca extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'log_busca';

    protected $fillable = ['total_tv','total_jornal','total_radio','total_web','fl_web','fl_radio','fl_jornal','fl_tv','cliente_id','termo','sentimento','data_inicio','data_fim','arquivo'];

}