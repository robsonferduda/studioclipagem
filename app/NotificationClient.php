<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationClient extends Model
{
    protected $table = 'notifications_client';
    protected $fillable = ['client_id','notification_id','valor','valor_atual','dt_inicio','dt_termino','status'];
    
    public function notification()
    {
        return $this->hasOne('App\Notification', 'id', 'notification_id');
    }
}