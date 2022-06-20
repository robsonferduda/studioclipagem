<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TokenController extends Controller
{

    public function checkFacebookToken(Request $request)
    {
        $token_app = getTokenApp();

        $url = "https://graph.facebook.com/debug_token";
        $params = [
            'input_token' => $request->page_token,
            'access_token' => $token_app

        ];

        $response = Http::get($url,$params);
       
        if(isset($response->json()['data']['error'])) {
            return json_encode([
                'is_valid' => $response->json()['data']['is_valid'], 
                'message' => $response->json()['data']['error']['message']
            ]);
        }
            
        $date = date("d-m-Y H:i:s ", $response->json()['data']['data_access_expires_at']);

        return json_encode([
            'is_valid' => true,
            'expires_at' => $date
        ]);
    } 
}
