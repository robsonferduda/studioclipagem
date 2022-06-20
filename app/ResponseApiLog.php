<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResponseApiLog extends Model
{
    protected $table = 'response_api_logs';
    protected $fillable = [
                            'status_code',
                            'content',
                            'url',
                            'params'                        
                        ];

}
