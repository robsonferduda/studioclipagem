<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WordCloudText extends Model
{
    protected $table = 'wordcloud_text';
    protected $fillable = [
                            'id',
                            'text'
                        ];
}