<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogExportacao extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'log_exportar_web';

    protected $fillable = ['total_coletado'];

}