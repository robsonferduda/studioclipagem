<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmissoraWeb extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'emissora_web';

    protected $fillable = ['nome_emissora','url_stream'];

}