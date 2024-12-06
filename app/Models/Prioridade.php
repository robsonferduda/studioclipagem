<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prioridade extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'prioridade';

    protected $fillable = [''];
}