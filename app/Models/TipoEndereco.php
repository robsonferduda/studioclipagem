<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoEndereco extends Model
{    
    protected $connection = 'pgsql';
    protected $table = 'tipo_endereco_eletronico_tee';

    protected $fillable = [''];     
    
    public function endereco()
    {
        return $this->belongsTo(EnderecoEletronico::class, 'id', 'id');
    }
}