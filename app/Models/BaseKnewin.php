<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseKnewin extends Model
{
    //use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'base_knewin';

    protected $fillable = ['id_knewin'];

}