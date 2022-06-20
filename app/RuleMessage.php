<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RuleMessage extends Model
{
    protected $table = 'rule_message';
    protected $fillable = [
                            'message_id',
                            'rule_id',
                            'rules_type'
                        ];
}
