<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoEmissoraWeb extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'videos_programa_emissora_web';

    protected $fillable = ['id'];

    public function programa()
    {
        return $this->hasOne(ProgramaEmissoraWeb::class, 'id', 'id_programa_emissora_web');
    }
}