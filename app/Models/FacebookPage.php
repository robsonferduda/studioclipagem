<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacebookPage extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'fb_pages_monitor';

    protected $fillable = [''];
}