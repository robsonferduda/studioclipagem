<?php

namespace App\Http\Controllers;

use App\Classes\IGMention;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IGWebhookController extends Controller
{

    public function receive(Request $request)
    {
        
        Log::warning($request);

        foreach($request['entry'] as $entry) {
            foreach($entry['changes'] as $change) {
                
                switch($change['field']){
                    case 'mentions': 
                        $this->mention($entry['id'], $change['value']);
                        break;
                }
            }
        }

        return $request->hub_challenge;

    }

    public function urlValidade(Request $request)
    {
        $token = '$a1b2C3d4e5f6$';
        
        if($request->hub_mode == 'subscribe' && $request->hub_verify_token == $token) {
            return $request->hub_challenge;
        }

        return '';
    }

    private function mention($id, $changes)
    {
        (new IGMention())->getMediaWebHook($id, $changes);
    }
   
}
