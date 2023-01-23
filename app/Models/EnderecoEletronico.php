<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnderecoEletronico extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'endereco_eletronico';

    protected $fillable = ['pessoa_id', 'endereco', 'tipo_id'];

    public $timestamps = false; // Verificar se as colunas de log não vão existir realmente

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'id', 'id');
    }

    public function tipo()
    {
        return $this->hasOne(TipoEndereco::class, 'id', 'id');
    }
}
