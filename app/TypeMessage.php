<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TypeMessage extends Model
{
    protected $table = 'type_message';
    protected $fillable = [
                            'name'
                        ];
}
