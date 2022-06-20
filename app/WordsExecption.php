<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WordsExecption extends Model
{
    protected $table = 'words_exception';
    protected $fillable = [
                            'word',
                            'client_id'
                        ];
}
