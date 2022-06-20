<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MediaHashtag extends Model
{
    protected $table = 'media_hashtag';

    protected $fillable = [ 'media_id',
                            'hashtag_id'
                        ];
}
