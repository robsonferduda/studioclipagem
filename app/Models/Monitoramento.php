<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Monitoramento extends Model
{    
    protected $connection = 'pgsql';
    protected $table = 'monitoramento';

    protected $fillable = [''];   
    
    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'id', 'id_cliente');
    }

    public function fontes()
    {
        return $this->hasMany(FontWeb::class, 'id', 'id_fila');
    }

    public function noticiaWeb()
    {
        return $this->hasMany(JornalWeb::class, 'id', 'monitoramento_id');
    }
}