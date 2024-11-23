<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaginaJornalImpresso extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'pagina_edicao_jornal_online';

    protected $fillable = [''];

    public function edicao()
    {
        return $this->belongsTo(EdicaoJornalImpresso::class, 'id_edicao_jornal_online');
    }
}