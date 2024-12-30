<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model
{
    protected $connection = 'pgsql';
    protected $table = 'role_user';

    public $fillable = ['role_id','user_id'];
}
