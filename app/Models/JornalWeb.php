<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JornalWeb extends Model
{    
    protected $connection = 'pgsql';
    protected $table = 'noticia_web';

    protected $fillable = [''];     
    
    public function fonte()
    {
        return $this->hasOne(Fonte::class, 'id', 'id_fonte');
    }
}