<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoEmissoraWeb extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'videos_emissora_web';

    protected $fillable = ['id'];

    public function emissora()
    {
        return $this->hasOne(EmissoraWeb::class, 'id', 'id_emissora_web');
    }

}