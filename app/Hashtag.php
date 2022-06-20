<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hashtag extends Model
{
    protected $table = 'hashtags';

    protected $fillable = [ 'hashtag',
                            'client_id',
                            'social_media_id'
                        ];

    public function medias()
    {
        return $this->belongsToMany('App\Media','media_hashtag','hashtag_id','media_id')->withTimestamps();
    }

    public function mediasTwitter()
    {
        return $this->belongsToMany('App\MediaTwitter','twitter_hashtag','hashtag_id','media_id')->withTimestamps();
    }

    public function socialMedia()
    {
        return $this->belongsTo('App\SocialMedia', 'social_media_id', 'id');
    }

    public function client()
    {
        return $this->belongsTo('App\Client', 'client_id', 'id');
    }

    public function pagePosts()
    {
        return $this->belongsToMany('App\FbPagePost','page_post_hashtag','hashtag_id','page_post_id')->withTimestamps();
    }

    public function pagePostsComments()
    {
        return $this->belongsToMany('App\FbPagePostComment','page_post_comment_hashtag','hashtag_id','page_post_comment_id')->withTimestamps();
    }
}
