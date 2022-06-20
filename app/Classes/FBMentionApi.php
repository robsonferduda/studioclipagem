<?php

namespace App\Classes;

use App\EndPoints;

class FBMentionApi extends IGApi{

    function __construct(String $id) {
        $this->id = $id;
    }

    public function getMentions(Array $params = []): Array
    {
        $url = EndPoints::getFBPagesTaggedLink($this->getId());
    
        $response = $this->makeApiCall($url,$params);
        
        if($response->successful()) {
            return $response->json();
        }
        
        return [];
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

    public function getPostMetionHooked($post_id, Array $params = []) : Array
    {
        $url = EndPoints::getPostMetionWebhookLink($post_id);
   
        $response = $this->makeApiCall($url,$params);
        
        if($response->successful()) {
            return $response->json();
        }
        
        return [];
    }

    public function getFbMentionFields()
    {
        $fields = [
            'post_id',
            'message',
            'permalink_url',
            'updated_time',
            'tagged_time'
        ];

        return implode(',',$fields);
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

    public function getFBReactionsFields()
    {
        $fields = [
            'id',
            'shares',
            'comments.summary(true)',
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