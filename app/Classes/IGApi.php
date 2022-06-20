<?php

namespace App\Classes;

use App\ResponseApiLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class IGApi{

    protected $id = '';

    protected function makeApiCall($url, $params): Response
    {     
        $response = Http::get($url,$params);
        
        if(!$response->successful()) {
            return $this->log($response, $url, $params);
        }
        
        return $response;
    }

    public function getAfter($response): String
    {
        return isset($response['paging']['cursors']['after'])
          ? $response['paging']['cursors']['after']
          : '';
    }

    public function hasAfter($response): Bool
    {
        return isset($response['paging']['cursors']['after'])
          ? true
          : false;
    }

    public function getBefore($response): String
    {
        return isset($response['paging']['cursors']['before'])
          ? $response['paging']['cursors']['before']
          : '';
    }

    public function hasBefore($response): Bool
    {
        return isset($response['paging']['cursors']['before'])
          ? true
          : false;
    }

    protected function getId(): String
    {
        return $this->id;
    }

    private function log(Response $response, String $url, Array $params)
    {
        ResponseApiLog::create([
            'status_code' => $response->status(),
            'content' => json_encode($response->json()),
            'url' => $url,
            'params' => json_encode($params)
        ]);

        return $response;
    }
}