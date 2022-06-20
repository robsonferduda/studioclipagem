<?php

namespace App\Classes;

use App\EndPoints;

class FBFeedApi extends IGApi{

    function __construct(String $id) {
        $this->id = $id;
    }

    public function getFeed(Array $params = []): Array
    {
        $url = EndPoints::getFBPagesFeedLink($this->getId());
    
        $response = $this->makeApiCall($url,$params);
        
        if($response->successful()) {
            return $response->json();
        }
        
        return [];
    }

    public function getFbPostFields()
    {
        $fields = [
            'id',
            'message',
            'permalink_url',
            'updated_time'
        ];

        return implode(',',$fields);
    } 

    public function getFBPostReactions($post_id, Array $params = []): Array
    {
        $url = EndPoints::getFBPostReactionsLink($post_id);
    
        $response = $this->makeApiCall($url,$params);
        
        if($response->successful()) {
            return $response->json();
        }
        
        return [];
    }

    public function getFBReactionsFields()
    {
        $fields = [           
            'shares',
            'comments.summary(true){created_time,message,comments{message,created_time,reactions.type(LIKE).limit(0).summary(true).as(LIKE),reactions.type(LOVE).limit(0).summary(true).as(LOVE),reactions.type(WOW).limit(0).summary(true).as(WOW),reactions.type(HAHA).limit(0).summary(true).as(HAHA),reactions.type(SAD).limit(0).summary(true).as(SAD),reactions.type(ANGRY).limit(0).summary(true).as(ANGRY),reactions.type(THANKFUL).limit(0).summary(true).as(THANKFUL)},reactions.type(LIKE).limit(0).summary(true).as(LIKE),reactions.type(LOVE).limit(0).summary(true).as(LOVE),reactions.type(WOW).limit(0).summary(true).as(WOW),reactions.type(HAHA).limit(0).summary(true).as(HAHA),reactions.type(SAD).limit(0).summary(true).as(SAD),reactions.type(ANGRY).limit(0).summary(true).as(ANGRY),reactions.type(THANKFUL).limit(0).summary(true).as(THANKFUL)}',
            'reactions.type(LIKE).limit(0).summary(true).as(LIKE)',
            'reactions.type(LOVE).limit(0).summary(true).as(LOVE)',
            'reactions.type(WOW).limit(0).summary(true).as(WOW)',
            'reactions.type(HAHA).limit(0).summary(true).as(HAHA)',
            'reactions.type(SAD).limit(0).summary(true).as(SAD)',
            'reactions.type(ANGRY).limit(0).summary(true).as(ANGRY)',
            'reactions.type(THANKFUL).limit(0).summary(true).as(THANKFUL)'
        ];

        return implode(',',$fields);
    }
}