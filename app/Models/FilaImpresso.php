<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FilaImpresso extends Model
{    
    protected $connection = 'pgsql';
    protected $table = 'fila_impresso';

    protected $fillable = ['dt_arquivo','ds_arquivo','id_fonte','ds_arquivo','tamanho','fl_processado'];     
    
    public function fonte()
    {
        return $this->hasOne(FonteImpressa::class, 'id', 'id_fonte');
    }
}