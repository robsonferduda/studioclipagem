<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SituacaoFonteWeb extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'situacao_fonte_web';

    protected $fillable = [''];

}