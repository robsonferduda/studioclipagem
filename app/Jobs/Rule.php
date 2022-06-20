<?php

namespace App\Jobs;

use App\Enums\TypeRule;
use App\FbComment;
use App\FbPagePost;
use App\FbPagePostComment;
use App\FbPost;
use App\IgComment;
use App\Media;
use App\MediaTwitter;
use App\Rule as AppRule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Notifications\RuleProcessNotification;

class Rule implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $client_id;
    public $timeout = 240;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($client_id)
    {
        $this->client_id = $client_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $rules = AppRule::with('expressions')->where('client_id', $this->client_id)->get();
        
        foreach($rules as $rule) {
            $todas = $rule->expressions()->wherePivot('type_rule_id', TypeRule::TODAS)->pluck('expression')->toArray();
            $algumas = $rule->expressions()->wherePivot('type_rule_id', TypeRule::ALGUMAS)->pluck('expression')->toArray();
            $nenhuma = $rule->expressions()->wherePivot('type_rule_id', TypeRule::NENHUMA)->pluck('expression')->toArray();

            // INSTAGRAM POSTS
            $medias = Media::where('client_id', $this->client_id);
            if(count($todas) > 0) {
                $medias = $medias->where(function ($query) use ($todas, $algumas) {
                    $query->where(function ($query) use ($todas) {
                        foreach($todas as $expression) {
                            $query->whereRaw(" lower(caption) SIMILAR TO '%({$expression} | {$expression}| {$expression} )%' ");
                        }        
                    });      
                    if(count($algumas) > 0) {
                        $query->orWhere(function ($query) use ($algumas) {
                            foreach($algumas as $expression) {
                                $query->orWhereRaw(" lower(caption) SIMILAR TO '%({$expression} | {$expression}| {$expression} )%' ");
                            }               
                         });
                     }
                });    
            }

            if(count($algumas) > 0 AND  count($todas) <= 0){
                $medias = $medias->where(function ($query) use ($algumas) {
                    foreach($algumas as $expression) {
                        $query->orWhereRaw(" lower(caption) SIMILAR TO '%({$expression} | {$expression}| {$expression} )%' ");
                    }               
                });
            }

            if(count($nenhuma) > 0) {
                $medias =  $medias->where(function ($query) use ($nenhuma) {
                    foreach($nenhuma as $expression) {
                        $query->whereRaw(" lower(caption) NOT SIMILAR TO '%({$expression} | {$expression}| {$expression} )%' ");
                    }                             
                });
            }
            //dd($medias->toSql());
            $ids = $medias->pluck('id')->toArray();

            $rule->igPosts()->sync($ids);

            // INSTAGRAM COMMENTS
            $medias = IgComment::whereHas('media', function($query){
                            $query->where('client_id', $this->client_id);
                        });
            if(count($todas) > 0) {
                $medias = $medias->where(function ($query) use ($todas, $algumas) {
                    $query->where(function ($query) use ($todas) {
                        foreach($todas as $expression) {
                            $query->whereRaw(" lower(text) SIMILAR TO '%({$expression} | {$expression}| {$expression} )%' ");
                        }        
                    });      
                    if(count($algumas) > 0) {
                        $query->orWhere(function ($query) use ($algumas) {
                            foreach($algumas as $expression) {
                                $query->orWhereRaw(" lower(text) SIMILAR TO '%({$expression} | {$expression}| {$expression} )%' ");
                            }               
                         });                       
                     }
                });    
            }

            if(count($algumas) > 0 AND  count($todas) <= 0){
                $medias = $medias->where(function ($query) use ($algumas) {
                    foreach($algumas as $expression) {
                        $query->orWhereRaw(" lower(text) SIMILAR TO '%({$expression} | {$expression}| {$expression} )%' ");
                    }               
                });
            }

            if(count($nenhuma) > 0) {
                $medias =  $medias->where(function ($query) use ($nenhuma) {
                    foreach($nenhuma as $expression) {
                       $query->whereRaw(" lower(text) NOT SIMILAR TO '%({$expression} | {$expression}| {$expression} )%' ");
                    }                             
                });
            }

            $ids = $medias->pluck('id')->toArray();

            $rule->igComments()->sync($ids);

            //FACEBOOK POSTS
            $medias = FbPost::where('client_id', $this->client_id);
            if(count($todas) > 0) {
                $medias = $medias->where(function ($query) use ($todas, $algumas) {
                    $query->where(function ($query) use ($todas) {
                        foreach($todas as $expression) {
                            $query->whereRaw(" lower(message) SIMILAR TO '%({$expression} | {$expression}| {$expression} )%' ");
                        }        
                    });      
                    if(count($algumas) > 0) {
                        $query->orWhere(function ($query) use ($algumas) {
                            foreach($algumas as $expression) {
                                $query->orWhereRaw(" lower(message) SIMILAR TO '%({$expression} | {$expression}| {$expression} )%' ");
                            }               
                         });
                     }
                });    
            }

            if(count($algumas) > 0 AND  count($todas) <= 0){
                $medias = $medias->where(function ($query) use ($algumas) {
                    foreach($algumas as $expression) {
                        $query->orWhereRaw(" lower(message) SIMILAR TO '%({$expression} | {$expression}| {$expression} )%' ");
                    }               
                });
            }

            if(count($nenhuma) > 0) {
                $medias =  $medias->where(function ($query) use ($nenhuma) {
                    foreach($nenhuma as $expression) {
                        $query->whereRaw(" lower(message) NOT SIMILAR TO '%({$expression} | {$expression}| {$expression} )%' ");
                    }                             
                });
            }

            $ids = $medias->pluck('id')->toArray();

            $rule->fbPosts()->sync($ids);

            //FACEBOOK COMMENTS
            $medias = FbComment::whereHas('fbPost', function($query){
                $query->where('client_id', $this->client_id);
            });
            if(count($todas) > 0) {
                $medias = $medias->where(function ($query) use ($todas, $algumas) {
                    $query->where(function ($query) use ($todas) {
                        foreach($todas as $expression) {
                            $query->whereRaw(" lower(text) SIMILAR TO '%({$expression} | {$expression}| {$expression} )%' ");
                        }        
                    });      
                    if(count($algumas) > 0) {
                        $query->orWhere(function ($query) use ($algumas) {
                            foreach($algumas as $expression) {
                                $query->orWhereRaw(" lower(text) SIMILAR TO '%({$expression} | {$expression}| {$expression} )%' ");
                            }               
                         });
                     }
                });    
            }

            if(count($algumas) > 0 AND  count($todas) <= 0){
                $medias = $medias->where(function ($query) use ($algumas) {
                    foreach($algumas as $expression) {
                        $query->orWhereRaw(" lower(text) SIMILAR TO '%({$expression} | {$expression}| {$expression} )%' ");
                    }               
                });
            }

            if(count($nenhuma) > 0) {
                $medias =  $medias->where(function ($query) use ($nenhuma) {
                    foreach($nenhuma as $expression) {
                        $query->whereRaw(" lower(text) NOT SIMILAR TO '%({$expression} | {$expression}| {$expression} )%' ");
                    }                             
                });
            }

            $ids = $medias->pluck('id')->toArray();

            $rule->fbComments()->sync($ids);

            //FACEBOOK PAGE POSTS
            $client_id = $this->client_id;
            $medias = FbPagePost::where(function($query) use ($client_id){
                $query->whereHas('terms', function ($query) use ($client_id){
                    $query->where('client_id', $client_id);
                })
                ->orWhereHas('hashtags', function ($query) use ($client_id){
                    $query->where('client_id', $client_id);
                });
            });
            
            if(count($todas) > 0) {
                $medias = $medias->where(function ($query) use ($todas, $algumas) {
                    $query->where(function ($query) use ($todas) {
                        foreach($todas as $expression) {
                            $query->whereRaw(" lower(message) SIMILAR TO '%({$expression} | {$expression}| {$expression} )%' ");
                        }        
                    });      
                    if(count($algumas) > 0) {
                        $query->orWhere(function ($query) use ($algumas) {
                            foreach($algumas as $expression) {
                                $query->orWhereRaw(" lower(message) SIMILAR TO '%({$expression} | {$expression}| {$expression} )%' ");
                            }               
                         });
                     }
                });    
            }

            if(count($algumas) > 0 AND  count($todas) <= 0){
                $medias = $medias->where(function ($query) use ($algumas) {
                    foreach($algumas as $expression) {
                        $query->orWhereRaw(" lower(message) SIMILAR TO '%({$expression} | {$expression}| {$expression} )%' ");
                    }               
                });
            }

            if(count($nenhuma) > 0) {
                $medias =  $medias->where(function ($query) use ($nenhuma) {
                    foreach($nenhuma as $expression) {
                        $query->whereRaw(" lower(message) NOT SIMILAR TO '%({$expression} | {$expression}| {$expression} )%' ");
                    }                             
                });
            }

            $ids = $medias->pluck('id')->toArray();
    
            $rule->fbPagePosts()->sync($ids);

             //FACEBOOK PAGE POSTS COMMENTS
             $medias = FbPagePostComment::where(function($query) use ($client_id){
                $query->whereHas('terms', function ($query) use ($client_id){
                    $query->where('client_id', $client_id);
                })
                ->orWhereHas('hashtags', function ($query) use ($client_id){
                    $query->where('client_id', $client_id);
                });
            });
            
            if(count($todas) > 0) {
                $medias = $medias->where(function ($query) use ($todas, $algumas) {
                    $query->where(function ($query) use ($todas) {
                        foreach($todas as $expression) {
                            $query->whereRaw(" lower(text) SIMILAR TO '%({$expression} | {$expression}| {$expression} )%' ");
                        }        
                    });      
                    if(count($algumas) > 0) {
                        $query->orWhere(function ($query) use ($algumas) {
                            foreach($algumas as $expression) {
                                $query->orWhereRaw(" lower(text) SIMILAR TO '%({$expression} | {$expression}| {$expression} )%' ");
                            }               
                         });
                     }
                });    
            }

            if(count($algumas) > 0 AND  count($todas) <= 0){
                $medias = $medias->where(function ($query) use ($algumas) {
                    foreach($algumas as $expression) {
                        $query->orWhereRaw(" lower(text) SIMILAR TO '%({$expression} | {$expression}| {$expression} )%' ");
                    }               
                });
            }

            if(count($nenhuma) > 0) {
                $medias =  $medias->where(function ($query) use ($nenhuma) {
                    foreach($nenhuma as $expression) {
                        $query->whereRaw(" lower(text) NOT SIMILAR TO '%({$expression} | {$expression}| {$expression} )%' ");
                    }                             
                });
            }

            $ids = $medias->pluck('id')->toArray();
    
            $rule->fbPagePostsComments()->sync($ids);

             //TWITTER POSTS
            $medias = MediaTwitter::where('client_id', $this->client_id);
            if(count($todas) > 0) {
                $medias = $medias->where(function ($query) use ($todas, $algumas) {
                    $query->where(function ($query) use ($todas) {
                        foreach($todas as $expression) {
                            $query->whereRaw(" lower(full_text) SIMILAR TO '%({$expression} | {$expression}| {$expression} )%' ");
                        }        
                    });      
                    if(count($algumas) > 0) {
                        $query->orWhere(function ($query) use ($algumas) {
                            foreach($algumas as $expression) {
                                $query->orWhereRaw(" lower(full_text) SIMILAR TO '%({$expression} | {$expression}| {$expression} )%' ");
                            }               
                        });
                    }
                });    
            }

            if(count($algumas) > 0 AND  count($todas) <= 0){
                $medias = $medias->where(function ($query) use ($algumas) {
                    foreach($algumas as $expression) {
                        $query->orWhereRaw(" lower(full_text) SIMILAR TO '%({$expression} | {$expression}| {$expression} )%' ");
                    }               
                });
            }
 
            if(count($nenhuma) > 0) {
                $medias =  $medias->where(function ($query) use ($nenhuma) {
                    foreach($nenhuma as $expression) {
                        $query->whereRaw(" lower(full_text) NOT SIMILAR TO '%({$expression} | {$expression}| {$expression} )%' ");
                    }                             
                });
            }

            $ids = $medias->pluck('id')->toArray();

            $rule->twPosts()->sync($ids);
            $rule->updated_at = date('Y-m-d H:i:s');

            //Bloco de atualização de rule, caso ainda não tenha sido atualizada e processada
            if(!$rule->fl_process){
                $rule->fl_process = true;
                $rule->save();
                $rule->notify(new RuleProcessNotification());   
            } 
        }
    }
}
