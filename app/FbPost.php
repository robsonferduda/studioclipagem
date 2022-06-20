<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class FbPost extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'fb_posts';

    protected $fillable = [ 'message',
                            'permalink_url',
                            'updated_time',
                            'tagged_time',
                            'client_id',
                            'post_id',
                            'mentioned',
                            'hooked',
                            'share_count',
                            'comment_count'
                        ];

    public function reactions()
    {
        return $this->belongsToMany('App\FbReaction','fb_post_reaction','post_id','reaction_id')->withTimestamps()->withPivot('count');
    }

    public function comments()
    {
        return $this->hasMany('App\FbComment','post_id','id');
    }

    public function getSentimentos($client_id, $data_inicial, $data_final)
    {
        $dt_inicial = $data_inicial->format('Y-m-d');
        $dt_final = $data_final->format('Y-m-d');
        
        return DB::select("SELECT sentiment, count(*) as total 
                            FROM fb_posts 
                            WHERE sentiment NOTNULL 
                            AND tagged_time BETWEEN '$dt_inicial 00:00:00' AND '$dt_final 23:59:59'
                            GROUP BY sentiment 
                            ORDER BY sentiment");
    }

    public function getReactions($client_id, $data_inicial, $data_final, $rule)
    {

        $dt_inicial = $data_inicial->format('Y-m-d');
        $dt_final = $data_final->format('Y-m-d');

        return DB::select("SELECT t3.name, t3.color, t3.icon, count(*) 
                              FROM fb_posts t1, 
                                   fb_post_reaction t2, 
                                   fb_reactions t3
                              WHERE t1.id = t2.post_id 
                              AND t2.reaction_id = t3.id 
                              AND t2.updated_at BETWEEN '$dt_inicial 00:00:00' AND '$dt_final 23:59:59'
                              AND t1.client_id = $client_id
                              GROUP BY t3.name, t3.color, t3.icon 
                              ORDER BY t3.name");
    }
}