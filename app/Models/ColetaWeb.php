<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ColetaWeb extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'coleta_web';

    protected $fillable = ['total_coletas'];

}