<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpressionRule extends Model
{
    protected $table = 'expressions_rules';
    protected $fillable = [
                            'rule_id',
                            'type_rule_id',
                            'expression'
                        ];
}
