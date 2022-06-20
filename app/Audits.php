<?php

namespace App;

use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Audits extends Model implements \OwenIt\Auditing\Contracts\Audit
{    
    use \OwenIt\Auditing\Audit;

    protected $table = 'audits';
    protected $primaryKey = 'id';

    /**
     * {@inheritdoc}
     */
    protected $guarded = [];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'old_values'   => 'json',
        'new_values'   => 'json',
        // Note: Please do not add 'auditable_id' in here, as it will break non-integer PK models
    ];

    protected $fillable = [
        'event', 'auditable_type', 'user_type', 'auditable_id', 'user_id', 'old_values', 'new_values','user_agent', 'url', 'ip_address'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}