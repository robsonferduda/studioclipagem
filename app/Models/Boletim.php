<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Boletim extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'boletim';

    protected $fillable = [''];

    public function situacao()
    {
        return $this->hasMany('App\SiatuacaoBoletim','media_id','id');
    }
}