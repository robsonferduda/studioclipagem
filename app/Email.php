<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    protected $table = 'emails';

    protected $fillable = [ 'ds_email',
                            'client_id',
                            'status'
                        ];

    public function client()
    {
        return $this->belongsTo('App\Client', 'client_id', 'id');
    }
}