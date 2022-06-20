<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TypeCollect extends Model
{
    protected $table = 'type_collect';
    protected $fillable = [
                            'name'
                        ];
}