<?php

namespace App\Classes;

use App\Client;
use App\Collect;
use App\Enums\SocialMedia;
use App\Enums\TypeCollect;
use App\Enums\TypeMessage;
use App\Media;

class IGHashTag{

    function __construct() {

    }

    public function pullMedias()
    {
        $hashtags_pulled = [];

        $clients = Client::get();

        foreach ($clients as $client) {

            foreach ($client->fbAccounts as $fbAccount) {

                foreach ($fbAccount->fbPages as $fbPage) {

                    if(isset($fbPage->igPage)){

                        $access_token = $fbAccount->token;
                        $id_user_id = $fbPage->igPage->page_id;

                        $hashtags = $client->hashtags()->where('social_media_id', SocialMedia::INSTAGRAM)->where('is_active',true)->get();

                        foreach ($hashtags as $hashtag) {

                            $total = 0;

                            if(in_array($hashtag->hashtag, $hashtags_pulled)){
                                continue;
                            }

                            $after = '';

                            $ig_hash_tag = new IGHashTagApi($id_user_id);
                            $params = [
                                'q' => $hashtag->hashtag,
                                'access_token' => $access_token,
                                'user_id' =>  $id_user_id,
                                'after' => $after
                            ];

                            $ig_hash_tag = new IGHashTagApi();
                            $id_hash_tag = $ig_hash_tag->getIdHashTag($params);

                            if(empty($id_hash_tag))
                                continue;

                            do {

                                $params = [
                                    'fields' => $ig_hash_tag->getIGHashTagFields(),
                                    'access_token' => $access_token,
                                    'after' => $after,
                                    'user_id' => $id_user_id,
                                    'limit' => 50
                                ];

                                $medias = $ig_hash_tag->getRecentMediaByHashTag($id_hash_tag, $params);

                                if(!isset($medias['data']) OR empty($medias['data'])) {
                                    break;
                                } else {
                                    if(!in_array($hashtag->hashtag, $hashtags_pulled)){
                                        $hashtags_pulled[] = $hashtag->hashtag;
                                    }
                                }

                                foreach ($medias['data'] as $media) {

                                    if(!empty($media['caption'])) {
                                        if(isLanguagePortuguese($media['caption']) == false)
                                            continue;
                                    }

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
                                        'hashtagged' => 'S'
                                    ]);

                                    if($media->wasRecentlyCreated) $total++;
                                    $media->hashtags()->syncWithoutDetaching($hashtag->id);
                                }

                                $after = $ig_hash_tag->getAfter($medias);

                            } while(count($medias['data']) >= 50);

                            $dados_coleta = array('id_type_collect' => TypeCollect::HASHTAG,
                                                 'id_social_media' => SocialMedia::INSTAGRAM,
                                                 'id_type_message' => TypeMessage::IG_POSTS,
                                                 'description' => $hashtag->hashtag,
                                                 'total' => $total,
                                                 'client_id' =>$hashtag->client_id );

                            Collect::create($dados_coleta);
                        }
                    }
                }
            }
        }
    }
}
