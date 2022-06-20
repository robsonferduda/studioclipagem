<?php

namespace App\Classes;

use App\Jobs\FbTerm as JobsFbTerm;

class FbTerm{

    function __construct() {
        
    }

    public function runJob()
    {
        JobsFbTerm::dispatch();                
    }

}