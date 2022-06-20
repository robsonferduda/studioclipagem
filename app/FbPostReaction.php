<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FbPostReaction extends Model
{
    protected $table = 'fb_post_reaction';

    protected $fillable = [ 'post_id','reaction_id','count'];
}