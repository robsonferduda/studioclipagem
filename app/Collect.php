<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collect extends Model
{
    use SoftDeletes;

    protected $table = 'collect';
    protected $fillable = ['id_type_collect','id_social_media','id_type_message','description','total','client_id'];

    public function client()
    {
        return $this->belongsTo('App\Client', 'client_id', 'id');
    }

    public function socialMedia()
    {
        return $this->belongsTo('App\SocialMedia', 'id_social_media', 'id');
    }

    public function type()
    {
        return $this->belongsTo('App\TypeCollect', 'id_type_collect', 'id_type_collect');
    }

    public function typeMessage()
    {
        return $this->belongsTo('App\TypeMessage', 'id_type_message', 'id');
    }
}