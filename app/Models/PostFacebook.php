<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostFacebook extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'post_facebook';

    protected $fillable = [''];

    public function pagina()
    {
        return $this->hasOne(FacebookPage::class, 'id', 'page_id');
    }
}