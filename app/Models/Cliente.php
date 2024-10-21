<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'clientes';
    protected $fillable = ['fl_ativo'];

    public function areas()
    {
        return $this->hasMany(ClienteArea::class, 'cliente_id', 'id');
    }
}
