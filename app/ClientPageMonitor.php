<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class ClientPageMonitor extends Model implements Auditable
{

    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'client_page_monitor';

    protected $fillable = [ 'fb_page_monitor_id',
                            'client_id'
                        ];

}