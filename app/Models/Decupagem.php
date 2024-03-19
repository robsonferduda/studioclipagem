<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Decupagem extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'decupagem';

    protected $fillable = ['arquivo'];

    public function noticiasTV()
    {
        return $this->hasMany(NoticiaTv::class, 'decupagem_id', 'id');
    }
}