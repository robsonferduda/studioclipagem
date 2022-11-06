<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Noticia extends Model
{
    const UPDATED_AT = null;
    
    protected $connection = 'mysql';
    protected $table = 'app_web';

    protected $fillable = ['status_envio'];                        
}