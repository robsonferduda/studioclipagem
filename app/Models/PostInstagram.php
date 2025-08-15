<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostInstagram extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'post_instagram';

    protected $fillable = [''];
}