<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $table = 'notifications_log';
    protected $fillable = ['client_id','id_notification','id_social_media','id_type_message','description','total'];
    
    public function client()
    {
        return $this->belongsTo('App\Client', 'client_id', 'id');
    }

    public function socialMedia()
    {
        return $this->belongsTo('App\SocialMedia', 'id_social_media', 'id');
    }

    public function typeMessage()
    {
        return $this->belongsTo('App\TypeMessage', 'id_type_message', 'id');
    }

    public function notification()
    {
        return $this->hasOne('App\Notification', 'id', 'id_notification');
    }
}