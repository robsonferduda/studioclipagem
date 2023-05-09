<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'clientes';

    protected $fillable = ['ativo', 'pessoa_id'];

    public $timestamps = false; // Verificar se as colunas de log não vão existir realmente

    public function pessoa()
    {
        return $this->hasOne(Pessoa::class, 'id', 'pessoa_id');
    }

    public function clienteArea()
    {
        return $this->hasMany(ClienteArea::class, 'cliente_id', 'id');
    }
}
