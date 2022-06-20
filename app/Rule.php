<?php

namespace App;

use App\Enums\TypeRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Rule extends Model
{
    use Notifiable;
    
    protected $table = 'rules';
    protected $fillable = [
                            'name',
                            'client_id',
                            'fl_process'
                        ];

    public function expressionsType($type)
    {
        return $this->hasMany('App\ExpressionRule','rule_id','id')->where('type_rule_id', $type);
    }

    public function expressions()
    {
        return $this->belongsToMany('App\TypeRule','expressions_rules','rule_id','type_rule_id')->withPivot('expression')->withTimestamps();
    }    

    public function igPosts()
    {
        return $this->morphedByMany('App\Media','rules' ,'rule_message', 'rule_id','message_id', 'id', 'id')->withTimestamps();
    }

    public function igComments()
    {
        return $this->morphedByMany('App\IgComment','rules' ,'rule_message', 'rule_id','message_id', 'id', 'id')->withTimestamps();
    }

    public function fbPosts()
    {
        return $this->morphedByMany('App\FbPost','rules' ,'rule_message', 'rule_id','message_id', 'id', 'id')->withTimestamps();
    }

    public function fbComments()
    {
        return $this->morphedByMany('App\FbComment','rules' ,'rule_message', 'rule_id','message_id', 'id', 'id')->withTimestamps();
    }
    
    public function twPosts()
    {
        return $this->morphedByMany('App\MediaTwitter','rules' ,'rule_message', 'rule_id','message_id', 'id', 'id')->withTimestamps();
    }

    public function fbPagePosts()
    {
        return $this->morphedByMany('App\FbPagePost','rules' ,'rule_message', 'rule_id','message_id', 'id', 'id')->withTimestamps();
    }

    public function fbPagePostsComments()
    {
        return $this->morphedByMany('App\FbPagePostComment','rules' ,'rule_message', 'rule_id','message_id', 'id', 'id')->withTimestamps();
    }

    public function getExpression()
    {
        $todas   = implode(' E ', $this->expressionsType(TypeRule::TODAS)->pluck('expression')->toArray());
        $todasRegra = '( '.$todas.' )';
        $algumas = implode(' OU ', $this->expressionsType(TypeRule::ALGUMAS)->pluck('expression')->toArray());
        $algumasRegra = '( '.$algumas.' )';
        $nenhuma = implode(' E ', $this->expressionsType(TypeRule::NENHUMA)->pluck('expression')->toArray());
        $nenhumaRegra = '( '.$nenhuma.' )';

        $or = '';
        $and  = '';
        $regra = '';

        if(!empty($todas) && !empty($algumas)) {
            $or = ' OU ';
        }

        if(!empty($todas) || !empty($algumas)) {

            if(empty($todas)) {
                $todasRegra = '';
            }

            if(empty($algumas)) {
                $algumasRegra = '';
            }

            $regra .= '( '. $todasRegra.$or.$algumasRegra.' )';
            $and = ' E ';
        }   

        if(!empty($nenhuma)) {
            $regra .= $and.' Não '.$nenhumaRegra;
        }
       
        return $regra;
    }
}