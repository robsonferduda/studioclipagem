<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pessoa extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'pessoas';

    protected $fillable = ['nome', 'cpf_cnpj'];

    public $timestamps = false; // Verificar se as colunas de log não vão existir realmente

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id', 'pessoa_id');
    }

    public function enderecoEletronico()
    {
        return $this->hasMany(EnderecoEletronico::class, 'id', 'id');
    }
}
