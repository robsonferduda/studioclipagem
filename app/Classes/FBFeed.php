<?php

namespace App\Classes;

use App\FbAccount;
use App\FbPageMonitor;
use App\FbPagePost;
use App\FbPagePostComment;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class FBFeed{

    public function pullMedias()
    {
        set_time_limit(0);

        $hour = \Carbon\Carbon::now()->format('H');

        $order = (int) $hour%2 == 0 ? 'ASC' : 'DESC';
        $limit = (int) $hour%2 == 0 ? 5 : 20;

        $pages = FbPageMonitor::orderBy('id', $order)->get();

        //$token_app = getTokenApp();

        $token = env('COLETA1');//$this->getTokenValid($token_app);

        foreach ($pages as $page) {

            $id_page_id = $page->page_id;

            $fb_feed = new FBFeedApi($id_page_id);
            $after = '';
             
            do {
                            
                $params = [
                    'fields' => $fb_feed->getFbPostFields().','.$fb_feed->getFBReactionsFields(),
                    'access_token' => $token,
                    'after' => $after,
                    'since' => \Carbon\Carbon::now()->subDay()->toDateString(),
                    'limit' => $limit
                ];
        
                $posts = $fb_feed->getFeed($params);
                
                if(empty($posts['data'])) {
                    continue;
                }

                foreach ($posts['data'] as $post) {                    
                    $reactions = $this->getReactions($post);
                    $comments = [];

                    // if(!empty($post['message'])) {
                    //     if(isLanguagePortuguese($post['message']) == false)
                    //         continue;
                    // }

                    if(isset($post['comments']['data'])) {
                        $comments = $post['comments'];
                    }
                                
                    $post = FbPagePost::updateOrCreate(
                            [
                                'post_id' => $post['id'],                                
                                'fb_page_monitor_id' => $page->id,
                            ],    
                            [
                                'message' => isset($post['message']) ? $post['message']: null,
                                'permalink_url' => isset($post['permalink_url']) ? $post['permalink_url']: null,
                                'updated_time' => isset($post['updated_time']) ? $post['updated_time']: null,                                                                                                            
                                'comment_count' => $reactions['qtd_comments'],
                                'share_count' => $reactions['qtd_shares'],
                            ]);    

                    foreach($comments['data'] as $comment) {

                        $reactions_comment = $this->getReactions($comment);

                        $comment_bd = FbPagePostComment::updateOrCreate(
                        [
                            'page_post_id' => $post['id'],
                            'created_time' => \Carbon\Carbon::parse($comment['created_time'])->toDateTimeString()
                        ],    
                        [
                            'text' => $comment['message'],
                           
                        ]); 

                        $reaction_comment_buffer = [];
                        foreach ($reactions_comment['types'] as $type => $qtd) {
                            if($qtd > 0) {
    
                                $reaction = constant('App\Enums\FbReaction::'. $type);
                                $reaction_comment_buffer[$reaction] = ['count' => $qtd];
                                           
                            }                                    
                        }
    
                        if (!empty($reaction_comment_buffer)) {
                            $comment_bd->reactions()->sync($reaction_comment_buffer);
                        }

                        if(isset($comment['comments']['data'])) {                            
                            foreach($comment['comments']['data'] as $relatedComment) {
                                
                                $reactions_comment_related = $this->getReactions($relatedComment);

                                $comment_bd_related = FbPagePostComment::updateOrCreate(
                                    [
                                        'page_post_id' => $post['id'],
                                        'related_to' => $comment_bd['id'],
                                        'created_time' => \Carbon\Carbon::parse($relatedComment['created_time'])->toDateTimeString()
                                    ],    
                                    [
                                        'text' => $relatedComment['message'],                                       
                                    ]); 


                                $reaction_comment_related_buffer = [];
                                foreach ($reactions_comment_related['types'] as $type => $qtd) {
                                    if($qtd > 0) {
                
                                        $reaction = constant('App\Enums\FbReaction::'. $type);
                                        $reaction_comment_related_buffer[$reaction] = ['count' => $qtd];
                                                       
                                    }                                    
                                }
                
                                if (!empty($reaction_comment_related_buffer)) {
                                    $comment_bd_related->reactions()->sync($reaction_comment_related_buffer);
                                }
                            }                            
                        }

                    }

                    $reaction_buffer = [];
                    foreach ($reactions['types'] as $type => $qtd) {
                        if($qtd > 0) {

                            $reaction = constant('App\Enums\FbReaction::'. $type);
                            $reaction_buffer[$reaction] = ['count' => $qtd];
                                       
                        }                                    
                    }

                    if (!empty($reaction_buffer)) {
                        $post->reactions()->sync($reaction_buffer);
                    }
                }            
                              
                $after = $fb_feed->getAfter($posts);
        
            } while($fb_feed->hasAfter($posts) && count($posts['data']) >= 50);
        }
    }

    public function getReactions($post_reactions) {

        $reactions = [
            'id' => isset($post_reactions['id']) ? isset($post_reactions['id']) : null,
            'qtd_shares' => isset($post_reactions['shares']['count']) ? $post_reactions['shares']['count'] : null,
            'qtd_comments' => isset($post_reactions['comments']['summary']['total_count']) ? $post_reactions['comments']['summary']['total_count'] : null,
            'types' => [
                'LIKE' => isset($post_reactions['LIKE']['summary']['total_count']) ? $post_reactions['LIKE']['summary']['total_count'] : null,
                'LOVE' => isset($post_reactions['LOVE']['summary']['total_count']) ? $post_reactions['LOVE']['summary']['total_count'] : null,
                'WOW' => isset($post_reactions['WOW']['summary']['total_count']) ? $post_reactions['WOW']['summary']['total_count'] : null,
                'HAHA' => isset($post_reactions['HAHA']['summary']['total_count']) ? $post_reactions['HAHA']['summary']['total_count'] : null,
                'SAD' => isset($post_reactions['SAD']['summary']['total_count']) ? $post_reactions['SAD']['summary']['total_count'] : null,
                'ANGRY' => isset($post_reactions['ANGRY']['summary']['total_count']) ? $post_reactions['ANGRY']['summary']['total_count'] : null,
                'THANKFUL' => isset($post_reactions['THANKFUL']['summary']['total_count']) ? $post_reactions['THANKFUL']['summary']['total_count'] : null
            ]
        ];

        return $reactions;
        
    }

    private function getTokenValid($token_app, $stepOne = true, Array $idsAccount = []) {

        if($stepOne) {
            $idsAccount = FbAccount::pluck('id')->toArray();
        }

        $key = array_rand($idsAccount);

        $account = FbAccount::where('id', $idsAccount[$key])->first();
       
        $url = "https://graph.facebook.com/debug_token";
        $params = [
            'input_token' => $account->token,
            'access_token' => $token_app
        ];

        $response = Http::get($url,$params);

        $response = $response->json();

        if($response['data']['is_valid'] == true) {
            return $account->token;
        } else  {
            unset($idsAccount[$key]);

            if(empty($idsAccount)) {
                return $token_app;
            }

            $this->getTokenValid($token_app, $stepOne = false, $idsAccount);
        }

    }

    public function fetchPostCount()
    {
        set_time_limit(0);

        $pages = DB::select("select 	
                                t0.id,	
                                count(distinct t1.id) as post_count ,
                                count(t2.id) as comment_count
                            from 
                                fb_pages_monitor t0
                                inner join fb_page_posts t1 on (t0.id = t1.fb_page_monitor_id)
                                left join fb_page_posts_comments t2 on (t1.id = t2.page_post_id)
                            where
                                t0.deleted_at is null
                            group by 
                                t0.id
                            having count(distinct t1.id) > 0
                            order by count(distinct t1.id) desc");

        foreach ($pages as $page) {

            $page_post_count = $page->post_count;
            $page_post_comment_count = $page->comment_count;

            DB::table('fb_pages_monitor')->where('id', $page->id)->update([
                'page_post_count' => $page_post_count,
                'page_post_comment_count' => $page_post_comment_count
            ]);
        }
    }
}