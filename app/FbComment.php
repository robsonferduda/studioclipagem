<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FbComment extends Model
{
    protected $table = 'fb_comments';

    protected $fillable = [ 'text',
                            'post_id',
                            'created_time',
                            'comment_id'                        
                        ];

    public function fbPost()
    {
        return $this->hasMany('App\FbPost','id','post_id');
    }
}
    