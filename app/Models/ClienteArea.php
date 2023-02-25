<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClienteArea extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'area_cliente';

    protected $fillable = [
        'cliente_id',
        'area_id',
        'expressao',
        'ativo',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id', 'id');
    }

}
