<?php

namespace App;

use DB;
use App\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class MediaTwitter extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'media_twitter';

    protected $fillable = [ 'twitter_id',
                            'full_text',
                            'retweet_count',
                            'favorite_count',
                            'user_id',
                            'user_name',
                            'user_screen_name',
                            'user_followers_count',
                            'user_friends_count',
                            'user_location',
                            'place_name',
                            'created_tweet_at',
                            'client_id',
                            'user_profile_image_url',
                            'permalink'
                        ];

    public function hashtags()
    {
        return $this->belongsToMany('App\Hashtag','twitter_hashtag','media_id','hashtag_id')->withTimestamps();
    }

    public function terms()
    {
        return $this->belongsToMany('App\Term','twitter_term','media_id','term_id')->withTimestamps();
    }

    public function getMedia($client_id)
    {
        return DB::select("SELECT count(*)/30 as media 
                           FROM media_twitter 
                           WHERE client_id = {$client_id} 
                           AND created_tweet_at > current_date - interval '30' day")[0]->media;
    }

    public function getSentimentos($client_id, $data_inicial, $data_final, $rule)
    {
        $dt_inicial = $data_inicial->format('Y-m-d');
        $dt_final = $data_final->format('Y-m-d');

        if($rule)
            $sql = "SELECT sentiment, count(*) as total 
                    FROM media_twitter t1, rule_message t2
                    WHERE t1.id = t2.id 
                    AND created_tweet_at BETWEEN '2021-01-01 00:00:00' AND '2022-01-01 23:59:59'
                    AND sentiment NOTNULL
                    AND t2.rule_id = $rule
                    AND t2.rules_type = 3
                    AND client_id = $client_id
                    GROUP BY sentiment 
                    ORDER BY sentiment";
        else
            $sql = "SELECT sentiment, count(*) as total 
                    FROM media_twitter 
                    WHERE sentiment NOTNULL 
                    AND created_tweet_at BETWEEN '$dt_inicial 00:00:00' AND '$dt_final 23:59:59'
                    AND client_id = $client_id
                    GROUP BY sentiment 
                    ORDER BY sentiment";

        return DB::select($sql);
    }

    public function getInfluenciadoresPositivos($client_id, $data_inicial, $data_final)
    {
        $dt_inicial = $data_inicial->format('Y-m-d');
        $dt_final = $data_final->format('Y-m-d');

        return DB::select("SELECT user_name, sentiment, user_profile_image_url, count(*) as total 
                            FROM media_twitter
                            WHERE sentiment IN(1)
                            AND client_id = $client_id
                            AND created_tweet_at BETWEEN '$dt_inicial 00:00:00' AND '$dt_final 23:59:59'
                            GROUP BY sentiment, user_profile_image_url, user_name
                            ORDER BY total DESC
                            LIMIT 10");
    }

    public function getInfluenciadoresNegativos($client_id, $data_inicial, $data_final)
    {
        $dt_inicial = $data_inicial->format('Y-m-d');
        $dt_final = $data_final->format('Y-m-d');

        return DB::select("SELECT user_name, sentiment, user_profile_image_url, count(*) as total 
                            FROM media_twitter
                            WHERE sentiment IN(-1)
                            AND client_id = $client_id
                            AND created_tweet_at BETWEEN '$dt_inicial 00:00:00' AND '$dt_final 23:59:59'
                            GROUP BY sentiment, user_profile_image_url, user_name
                            ORDER BY total DESC
                            LIMIT 10");
    }

    public function getTweetLocation($client_id, $data_inicial, $data_final, $rule)
    {
        $dt_inicial = $data_inicial->format('Y-m-d');
        $dt_final = $data_final->format('Y-m-d');

        return DB::select("SELECT place_name, count(*) as total
                            FROM media_twitter 
                            WHERE place_name notnull 
                            AND place_name != '' 
                            AND client_id = $client_id
                            AND created_tweet_at BETWEEN '$dt_inicial 00:00:00' AND '$dt_final 23:59:59'
                            GROUP BY place_name
                            ORDER BY total DESC
                            LIMIT 20");

    }

    public function getUserLocation($client_id, $data_inicial, $data_final, $rule_id)
    {
        $dados = array();
        $dt_inicial = $data_inicial->format('Y-m-d');
        $dt_final = $data_final->format('Y-m-d');

        if($rule_id){
            
            $sql = "SELECT user_location, count(*) as total 
                    FROM media_twitter t1, rule_message t2
                    WHERE t1.id = t2.id 
                    AND user_location notnull 
                    AND user_location != ''
                    AND client_id = $client_id
                    AND created_tweet_at BETWEEN '$dt_inicial 00:00:00' AND '$dt_final 23:59:59'
                    AND t2.rule_id = $rule_id
                    AND t2.rules_type = 3
                    GROUP BY user_location
                    ORDER BY total DESC
                    LIMIT 20";

        }else{
            $sql = "SELECT user_location, count(*) as total 
                                FROM media_twitter 
                                WHERE user_location notnull 
                                AND user_location != ''
                                AND client_id = $client_id
                                AND created_tweet_at BETWEEN '$dt_inicial 00:00:00' AND '$dt_final 23:59:59'
                                GROUP BY user_location
                                ORDER BY total DESC
                                LIMIT 20";
        }
        
        return DB::select($sql);
    }
}