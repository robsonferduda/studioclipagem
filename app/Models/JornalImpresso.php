<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JornalImpresso extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'noticia_impresso';

    protected $fillable = [''];

    public function fonte()
    {
        return $this->hasOne(Fonte::class, 'id', 'id_fonte');
    }

    public function fila()
    {
        return $this->belongsTo(FilaImpresso::class, 'id_fila', 'id');
    }
}
