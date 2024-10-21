<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SituacaoBoletim extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'situacao_boletim';

    protected $fillable = [''];

}