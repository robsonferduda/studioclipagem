<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FbPagePostReaction extends Model
{
    protected $table = 'fb_page_post_reaction';
    protected $fillable = [ 'page_post_id','reaction_id','count'];
}