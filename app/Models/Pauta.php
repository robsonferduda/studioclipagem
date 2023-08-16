<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pauta extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'pauta';

    protected $fillable = ['descricao'];

    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'id', 'cliente_id');
    }
}