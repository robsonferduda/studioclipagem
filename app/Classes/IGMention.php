<?php

namespace App\Classes;

use App\Media;
use App\Client;
use App\IgPage;
use App\IgComment;
use App\Collect;
use App\Enums\SocialMedia;
use App\Enums\TypeCollect;
use App\Enums\TypeMessage;
use Illuminate\Support\Facades\Log;

class IGMention{

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

                    if(isset($fbPage->igPage)){

                        $total = 0;

                        $access_token = $fbAccount->token;
                        $id_user_id = $fbPage->igPage->page_id;

                        $ig_mention = new IGMentionApi($id_user_id);
                        $after = '';

                        $medias_buffer = [];
                        $medias_limit_not_ordered = 0;

                        do {
                            
                            $params = [
                                'fields' => $ig_mention->getIGMentionFields(),
                                'access_token' => $access_token,
                                'after' => $after,
                                'since' => \Carbon\Carbon::now()->subDay()->toDateString(),
                                'limit' => 50
                            ];
        
                            $medias = $ig_mention->getMentions($params);
                                              
                            foreach ($medias['data'] as $media) {

                                $date_created = new \DateTime($media['timestamp']);

                                $strtotime_date_created =  strtotime($date_created->format('Y-m-d'));
                                $strtotime_date_yesterday =  strtotime(\Carbon\Carbon::now()->subDay()->format('Y-m-d'));
                                
                                if($strtotime_date_created < $strtotime_date_yesterday) {
                                    $medias_buffer[] = $media;
                                    $medias_limit_not_ordered++;
                                } else {
                                 
                                    $media = Media::updateOrCreate(
                                        [
                                            'media_id' => $media['id'],
                                            'client_id' => $client->id
                                        ],    
                                        [
                                            'caption' => isset($media['caption']) ? $media['caption']: null,
                                            'comments_count' => isset($media['comments_count']) ? $media['comments_count']: null,
                                            'media_product_type' => isset($media['media_product_type']) ? $media['media_product_type']: null,                    
                                            'like_count' => isset($media['like_count']) ? $media['like_count']: null,
                                            'media_type' => isset($media['media_type']) ? $media['media_type']: null,
                                            'media_url' => isset($media['media_url']) ? $media['media_url'] : null,
                                            'timestamp' =>  isset($media['timestamp']) ? $media['timestamp']: null,
                                            'permalink' =>  isset($media['permalink']) ? $media['permalink']: null,
                                            'username' =>  isset($media['username']) ? $media['username']: null,
                                            'video_title' =>  isset($media['video_title']) ? $media['video_title']: null,                                        
                                            'mentioned' => 'S'
                                        ]);                                  
                                    $medias_limit_not_ordered = 0;
                                }                           
                            }            
                               
                            $after = $ig_mention->getAfter($medias);
        
                        } while($ig_mention->hasAfter($medias) && ($strtotime_date_created >= $strtotime_date_yesterday) && $medias_limit_not_ordered <= 5);
                       
                        foreach($medias_buffer as $media) {

                            $media = Media::updateOrCreate(
                                [
                                    'media_id' => $media['id'],
                                    'client_id' => $client->id
                                ],    
                                [
                                    'caption' => isset($media['caption']) ? $media['caption']: null,
                                    'comments_count' => isset($media['comments_count']) ? $media['comments_count']: null,
                                    'media_product_type' => isset($media['media_product_type']) ? $media['media_product_type']: null,                    
                                    'like_count' => isset($media['like_count']) ? $media['like_count']: null,
                                    'media_type' => isset($media['media_type']) ? $media['media_type']: null,
                                    'media_url' => isset($media['media_url']) ? $media['media_url'] : null,
                                    'timestamp' =>  isset($media['timestamp']) ? $media['timestamp']: null,
                                    'permalink' =>  isset($media['permalink']) ? $media['permalink']: null,
                                    'username' =>  isset($media['username']) ? $media['username']: null,
                                    'video_title' =>  isset($media['video_title']) ? $media['video_title']: null,                                    
                                    'mentioned' => 'S'
                            ]);    
                            
                            if($media->wasRecentlyCreated) $total++; 
                        }

                        $dados_coleta = array('id_type_collect' => TypeCollect::HASHTAG,
                                                 'id_social_media' => SocialMedia::INSTAGRAM,
                                                 'id_type_message' => TypeMessage::IG_COMMENT,
                                                 'description' => '',
                                                 'total' => $total,
                                                 'client_id' => $client->id);            

                        Collect::create($dados_coleta);
                    }
                }
            }
        }
    }

    public function getMediaWebHook($id, $changes)
    {
        //$changes['media_id'] = '18194579986190935';
        //$id = '17841437726599322';

        $ig_mention = new IGMentionApi($id);

        $igPages =  IgPage::with('fbPage.fbAccount')->where('page_id', $id)->get();

        foreach($igPages as $igPage) {

            if($igPage->fbPage->fbAccount->mention !== true) {
                continue;
            }
            
            Log::warning($igPage);
            
            if(isset($changes['comment_id'])) {

                $params = [
                    'fields' => "mentioned_comment.comment_id({$changes['comment_id']}){text,timestamp,id,media{{$ig_mention->getIGMentionFields()}}}",
                    'access_token' => $igPage->fbPage->fbAccount->token
                ];

                $comment = $ig_mention->getMetionHooked($params);

                $media = $comment['mentioned_comment']['media'];  
                $comment =  $comment['mentioned_comment'];              
                
            } else {

                $params = [
                    'fields' => "mentioned_media.media_id({$changes['media_id']}){{$ig_mention->getIGMentionFields()}}",
                    'access_token' => $igPage->fbPage->fbAccount->token
                ];

                $media = $ig_mention->getMetionHooked($params);

                $media = $media['mentioned_media'];
            }                               

            $media = Media::updateOrCreate(
                [
                    'media_id' => $media['id'],
                    'client_id' => $igPage->fbPage->fbAccount->client_id
                ],    
                [
                    'caption' => isset($media['caption']) ? $media['caption']: null,
                    'comments_count' => isset($media['comments_count']) ? $media['comments_count']: null,
                    'media_product_type' => isset($media['media_product_type']) ? $media['media_product_type']: null,                    
                    'like_count' => isset($media['like_count']) ? $media['like_count']: null,
                    'media_type' => isset($media['media_type']) ? $media['media_type']: null,
                    'media_url' => isset($media['media_url']) ? $media['media_url'] : null,
                    'timestamp' =>  isset($media['timestamp']) ? $media['timestamp']: null,
                    'permalink' =>  isset($media['permalink']) ? $media['permalink']: null,
                    'username' =>  isset($media['username']) ? $media['username']: null,
                    'video_title' =>  isset($media['video_title']) ? $media['video_title']: null,                                    
                    'mentioned' => 'S',
                    'hooked' => 'S'
            ]); 
            
            if(isset($changes['comment_id'])) {
                $comment = IgComment::updateOrCreate(
                    [
                        'media_id' => $media['id'],
                        'comment_id' => $comment['id']
                    ],    
                    [
                        'text' => isset($comment['text']) ? $comment['text']: null,
                        'timestamp' => isset($comment['timestamp']) ? $comment['timestamp']: null
                ]); 
            }        
        }
    }
}