<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FonteImpressa extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'fonte_impressa';

    protected $fillable = ['codigo', 'nome', 'cd_cidade', 'cd_estado'];

    public $timestamps = false;
}
