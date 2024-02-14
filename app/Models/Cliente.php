<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'clientes';

    protected $fillable = ['ativo', 'pessoa_id','logo','logo_expandida'];

    public $timestamps = false; // Verificar se as colunas de log não vão existir realmente

    public function pessoa()
    {
        return $this->hasOne(Pessoa::class, 'id', 'pessoa_id')->orderBy('nome');
    }

    public function areas()
    {
        return $this->hasMany(ClienteArea::class, 'cliente_id', 'id');
    }
}
