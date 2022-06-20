<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Boletim extends Model
{
    const UPDATED_AT = null;
    
    protected $connection = 'mysql';
    protected $table = 'app_boletins';

    protected $fillable = ['status_envio'];
}