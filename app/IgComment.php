<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IgComment extends Model
{
    protected $table = 'ig_comments';

    protected $fillable = [ 'text',
                            'media_id',
                            'comment_id',
                            'timestamp'                         
                        ];
    
    public function media()
    {
        return $this->belongsTo('App\Media', 'media_id', 'id');
    }   
}