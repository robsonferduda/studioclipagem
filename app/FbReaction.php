<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FbReaction extends Model
{
    protected $table = 'fb_reactions';

    protected $fillable = [ 'name'];
}