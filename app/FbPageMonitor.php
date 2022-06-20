<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class FbPageMonitor extends Model
{

    use SoftDeletes;

    protected $table = 'fb_pages_monitor';

    protected $fillable = [ 'name',
                            'url',
                            'mention',
                            'post',
                            'page_id',
                            'picture_url',
                            'page_post_count',
                            'page_post_comment_count'
                        ];

    public function clients () 
    {
        return $this->belongsToMany(Client::class, 'client_page_monitor', 'fb_page_monitor_id', 'client_id')->withTimestamps();
    }

    public function fbPagesPost()
    {
        return $this->hasMany('App\FbPagePost', 'fb_page_monitor_id', 'id');
    }

}
