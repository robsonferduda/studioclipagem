<?php

namespace App\Classes;

use App\EndPoints;

class IGMentionApi extends IGApi{

    function __construct(String $id) {
        $this->id = $id;
    }

    public function getMentions(Array $params = []): Array
    {
        $url = EndPoints::getMetionsLink($this->getId());
    
        $response = $this->makeApiCall($url,$params);
        
        if($response->successful()) {
            return $response->json();
        }
        
        return [];
    }

    public function getMetionHooked(Array $params = []) : Array
    {
        $url = EndPoints::getMetionWebhookLink($this->getId());
   
        $response = $this->makeApiCall($url,$params);
        
        if($response->successful()) {
            return $response->json();
        }
        
        return [];
    }


    public function getIGMentionFields()
    {
        $fields = [
            'caption',
            'comments_count',
            'media_product_type',
            'id',
            'like_count',
            'media_type',
            'media_url',
            'permalink',
            'timestamp',
            'username',
            'video_title'
        ];

        return implode(',',$fields);
    }
}