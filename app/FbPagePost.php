<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class FbPagePost extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'fb_page_posts';

    protected $fillable = [ 'message',
                            'fb_page_monitor_id',
                            'permalink_url',
                            'updated_time',                            
                            'post_id',
                            'mentioned',
                            'hooked',
                            'share_count',
                            'comment_count'
                        ];

    public function reactions()
    {
        return $this->belongsToMany('App\FbReaction','fb_page_post_reaction','page_post_id','reaction_id')->withTimestamps()->withPivot('count');
    }

    public function page()
    { 
        return $this->hasOne('App\FbPageMonitor', 'id', 'fb_page_monitor_id'); 
    }

    public function terms()
    {
        return $this->belongsToMany('App\Term','page_post_term','page_post_id','term_id')->withTimestamps();
    }

    public function hashtags()
    {
        return $this->belongsToMany('App\Hashtag','page_post_hashtag','page_post_id','hashtag_id')->withTimestamps();
    }

    public function fbPagePostComment()
    {
        return $this->hasMany('App\FbPagePostComment','page_post_id','id');
    }

    public function getReactions($client_id, $data_inicial, $data_final, $rule)
    {

        $dt_inicial = $data_inicial->format('Y-m-d');
        $dt_final = $data_final->format('Y-m-d');

        return DB::select(" SELECT t3.name, t3.color, t3.icon, count(*)
                            FROM fb_page_posts t1, 
                                fb_page_post_reaction t2, 
                                fb_reactions t3,
                                client_page_monitor t4
                            WHERE t1.id = t2.page_post_id 
                            AND t2.reaction_id = t3.id 
                            AND t1.fb_page_monitor_id = t4.fb_page_monitor_id 
                            AND t1.updated_time BETWEEN '$dt_inicial 00:00:00' AND '$dt_final 23:59:59'
                            AND t4.client_id = $client_id
                            GROUP BY t3.name, t3.color, t3.icon");
    }
}