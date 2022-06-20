<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FbPagePostCommentReaction extends Model
{
    protected $table = 'fb_page_post_comment_reaction';
    protected $fillable = [ 'page_post_comment_id','reaction_id','count'];
}