<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class JornalWeb extends Model
{    
    protected $connection = 'pgsql';
    protected $table = 'noticias_web';

    protected $fillable = ['cd_estado'];     
    
    public function fonte()
    {
        return $this->hasOne(FonteWeb::class, 'id', 'id_fonte');
    }
} 