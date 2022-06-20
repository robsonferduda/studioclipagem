<?php

namespace App\Classes;

use App\Client;
use App\Jobs\Rule as JobsRule;

class Rule{

    function __construct() {
        
    }

    public function runJob()
    {
        $clients = Client::get();

        foreach ($clients as $client) {            
            JobsRule::dispatch($client->id);
        }
    }

}