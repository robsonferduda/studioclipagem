<?php

namespace App\Classes;

use App\Client;
use App\FbComment;
use App\FbPost;
use App\FbPage;
use Illuminate\Support\Facades\Log;

class FBMention{

    function __construct() {
        
    }

    public function pullMedias()
    {
        $clients = Client::get();

        foreach ($clients as $client) {
            
            foreach ($client->fbAccounts as $fbAccount) {

                if($fbAccount->mention !== true) {
                    continue;
                }

                foreach ($fbAccount->fbPages as $fbPage) {

                    $access_token = $fbPage->token;
                    $id_page_id = $fbPage->page_id;

                    $fb_mention = new FBMentionApi($id_page_id);
                    $after = '';
                        
                    do {
                            
                        $params = [
                            'fields' => $fb_mention->getFBMentionFields(),
                            'access_token' => $access_token,
                            'after' => $after,
                            'since' => \Carbon\Carbon::now()->subDay()->toDateString(),
                            'limit' => 50
                        ];
        
                        $posts = $fb_mention->getMentions($params);
                        
                        foreach ($posts['data'] as $post) {

                                $date_tagged = new \DateTime($post['tagged_time']);

                                $strtotime_date_tagged =  strtotime($date_tagged->format('Y-m-d'));
                                $strtotime_date_yesterday =  strtotime(\Carbon\Carbon::now()->subDay()->format('Y-m-d'));

                                $reactions = $this->getReactions($post['id'], $fb_mention, $access_token);
                                                               
                                $post = FbPost::updateOrCreate(
                                        [
                                            'post_id' => $post['id'],
                                            'client_id' => $client->id
                                        ],    
                                        [
                                            'message' => isset($post['message']) ? $post['message']: null,
                                            'permalink_url' => isset($post['permalink_url']) ? $post['permalink_url']: null,
                                            'updated_time' => isset($post['updated_time']) ? $post['updated_time']: null,                    
                                            'tagged_time' => isset($post['tagged_time']) ? $post['tagged_time']: null,                            
                                            'mentioned' => 'S',
                                            'comment_count' => $reactions['qtd_comments'],
                                            'share_count' => $reactions['qtd_shares'],
                                        ]);    

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
                               
                        $after = $fb_mention->getAfter($posts);
        
                    } while($fb_mention->hasAfter($posts) && ($strtotime_date_tagged >= $strtotime_date_yesterday));
                       
                }
            }
        }
    }

    public function getReactions($post_id, $fb_mention, $access_token) {

        $params = [
            'fields' => $fb_mention->getFBReactionsFields($post_id),
            'access_token' => $access_token
        ];

        $post_reactions = $fb_mention->getFBPostReactions($post_id,$params);

        $reactions = [
            'id' => $post_reactions['id'],
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

    public function getPostWebHook($id, $changes)
    {
        $fb_mention = new FBMentionApi($id);

        $fbPages =  FbPage::with('fbAccount')->where('page_id', $id)->get();

        foreach($fbPages as $fbPage) {

            $access_token = $fbPage->token;
            $client_id = $fbPage->fbAccount->client_id;

            if($fbPage->fbAccount->mention !== true) {
                continue;
            }

            $params = [
                'fields' => $fb_mention->getFbPostFields(),
                'access_token' => $access_token,
            ];

            if(!isset($changes['comment_id'])) {

                $post = $fb_mention->getPostMetionHooked($changes['post_id'], $params);

                $reactions = $this->getReactions($changes['post_id'], $fb_mention, $access_token);

                $post = FbPost::updateOrCreate(
                    [
                        'post_id' => $post['id'],
                        'client_id' => $client_id
                    ],    
                    [
                        'message' => isset($post['message']) ? $post['message']: null,
                        'permalink_url' => isset($post['permalink_url']) ? $post['permalink_url']: null,
                        'updated_time' => isset($post['updated_time']) ? $post['updated_time']: null,                                                                
                        'mentioned' => 'S',
                        'hooked' => 'S',
                        'comment_count' => $reactions['qtd_comments'],
                        'share_count' => $reactions['qtd_shares'],
                ]);    


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
                               
            if(isset($changes['comment_id'])) {

                $post = FbPost::updateOrCreate(
                    [
                        'post_id' => '99_'.$changes['comment_id'],
                        'client_id' => $client_id
                    ],    
                    [
                        'message' => '',
                        'permalink_url' => '',
                        'updated_time' => \Carbon\Carbon::now(),                                                                
                        'mentioned' => 'S',
                        'hooked' => 'S',
                ]);   

                $comment = FbComment::updateOrCreate(
                    [
                        'post_id' => $post['id'],
                        'comment_id' => $changes['comment_id']
                    ],    
                    [
                        'text' => isset($changes['message']) ? $changes['message']: null,
                        'created_time' => isset($changes['created_time']) ?  date('Y-m-d H:i:s', $changes['created_time']) : null
                ]); 
            }                      
        }
    }
}