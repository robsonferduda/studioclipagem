<?php

namespace App\Classes;

use App\EndPoints;

class IGHashTagApi extends IGApi{

    function __construct() {
        
    }

    public function getIdHashTag(Array $params): String
    {
        $url = EndPoints::getSearchIdHashTagLink();
        
        $response = $this->makeApiCall($url,$params);

        if($response->successful()) {
            return $response->json()['data'][0]['id'];
        }
        
        return '';
    }

    public function getRecentMediaByHashTag(String $id ,Array $params): Array
    {
        $url = EndPoints::getRecentMediaByHashTagLink($id);
    
        $response = $this->makeApiCall($url,$params);
        
        if($response->successful()) {
            return $response->json();
        }
        
        return [];
    }

    public function getIGHashTagFields(): String 
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
            'timestamp'
        ];

        return implode(',',$fields);
    }
}