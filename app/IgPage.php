<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IgPage extends Model
{
    protected $table = 'ig_pages';

    protected $fillable = [ 'name',
                            'page_id',
                            'fb_page_id'
                        ];

    public function fbPage()
    {
        return $this->hasOne(FbPage::class, 'id', 'fb_page_id');
    }
}
