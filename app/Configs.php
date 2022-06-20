<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class configs extends Model
{
    use SoftDeletes;

    protected $table = 'configs';
    protected $fillable = ['key','value'];

}