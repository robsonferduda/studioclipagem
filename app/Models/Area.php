<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'area';

    protected $fillable = ['descricao'];

    public function clienteArea()
    {
        return $this->belongsTo(ClienteArea::class, 'id', 'area_id');
    }
}