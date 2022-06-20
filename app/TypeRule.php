<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TypeRule extends Model
{
    protected $table = 'type_rules';
    protected $fillable = [
                            'name'
                        ];
}
