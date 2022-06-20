<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialMedia extends Model
{
    protected $table = 'social_media';
    protected $fillable = ['name'];

    public function hashtags()
    {
        return $this->hasOne('App\Hashtag', 'social_media_id', 'id');
    }
}
