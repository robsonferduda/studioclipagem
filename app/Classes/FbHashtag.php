<?php

namespace App\Classes;

use App\Jobs\FbHashtag as JobsFbHashtag;

class FbHashtag{

    function __construct() {
        
    }

    public function runJob()
    {
        JobsFbHashtag::dispatch();                
    }

}