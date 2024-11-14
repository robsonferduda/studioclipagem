<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmissoraWeb extends Model
{
    use SoftDeletes;
    use Searchable;

    protected $connection = 'pgsql';
    protected $table = 'emissora_web';

    protected $fillable = ['id_fonte'];

    public function getScoutKey(): string
    {
        return $this->nome_emissora;
    }

    public function getScoutKeyName(): string
    {
        return 'nome_emissora';
    }

}