<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FbPage extends Model
{
    protected $table = 'fb_pages';

    protected $fillable = [ 'name',
                            'page_id',
                            'fb_account_id',
                            'token'
                        ];

    public function igPage()
    {
        return $this->hasOne(IgPage::class, 'fb_page_id', 'id');
    }   
    
    public function fbAccount()
    {
        return $this->hasOne(FbAccount::class, 'id', 'fb_account_id');
    }   
}
