<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientFbPage extends Model
{
    protected $table = 'client_fb_pages';

    protected $fillable = [ 'url',
                            'mention',
                            'post',
                            'client_id'                         
                        ];                                                            
}
