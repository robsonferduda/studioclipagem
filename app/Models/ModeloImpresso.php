<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModeloImpresso extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'modelos_jornal_online';

    protected $fillable = [''];

}