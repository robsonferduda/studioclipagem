<?php

namespace App;

class EndPoints
{
    const BASE_URL = 'https://graph.facebook.com/v12.0/';
    const MENTION_URL = '{ig_user_id}/tags';
    const MENTION_WEBHOOK_URL = '{ig_user_id}';
    const SEARCH_ID_HASHTAG_URL = 'ig_hashtag_search';
    const RECENT_MEDIA_BY_HASHTAG_URL = '{ig_hashtag_id}/recent_media';
    const FB_PAGES_URL = 'me/accounts';
    const IG_BUSINESS_ACCOUNT_URL = '{fb_page_id}';
    const SUBSCRIBE_APPS_URL = '{page_id}/subscribed_apps';
    const FB_PAGE_TAGGED = '{fb_page_id}/tagged';
    const FB_PAGE_FEED = '{fb_page_id}/feed';
    const FB_POST_REACTIONS = '{fb_post_id}';
    const FB_POST_METION_HOOKED = '{fb_post_id}';
    const FB_SEARCH_PAGES = 'pages/search';
    const FB_SEARCH_PAGE_INFO = '{fb_page_id}';
    const FB_PAGE_PICTURE ='{fb_page_id}/picture';

    
    public static function getMetionsLink($ig_user_id)
    {
        return str_replace('{ig_user_id}', urlencode($ig_user_id), static::BASE_URL.static::MENTION_URL);
    }

    public static function getMetionWebhookLink($ig_user_id)
    {
        return str_replace('{ig_user_id}', urlencode($ig_user_id), static::BASE_URL.static::MENTION_WEBHOOK_URL);
    }

    public static function getFBPagesFeedLink($fb_page_id)
    {
        return str_replace('{fb_page_id}', urlencode($fb_page_id), static::BASE_URL.static::FB_PAGE_FEED);
    }

    public static function getSearchIdHashTagLink()
    {
        return  static::BASE_URL.static::SEARCH_ID_HASHTAG_URL;
    }

    public static function getRecentMediaByHashTagLink($ig_hashtag_id)
    {
        return str_replace('{ig_hashtag_id}', urlencode($ig_hashtag_id), static::BASE_URL.static::RECENT_MEDIA_BY_HASHTAG_URL);
    }

    public static function getFBPagesLink()
    {
        return  static::BASE_URL.static::FB_PAGES_URL;
    }

    public static function getIGBusinessAccountLink($fb_page_id)
    {
        return str_replace('{fb_page_id}', urlencode($fb_page_id), static::BASE_URL.static::IG_BUSINESS_ACCOUNT_URL);
    }

    public static function getSubscribeAppsLink($page_id)
    {
        return str_replace('{page_id}', urlencode($page_id), static::BASE_URL.static::SUBSCRIBE_APPS_URL);
    }

    public static function getFBPagesTaggedLink($fb_page_id)
    {
        return str_replace('{fb_page_id}', urlencode($fb_page_id), static::BASE_URL.static::FB_PAGE_TAGGED);
    }

    public static function getFBPostReactionsLink($post_id)
    {
        return str_replace('{fb_post_id}', urlencode($post_id), static::BASE_URL.static::FB_POST_REACTIONS);
    }

    public static function getPostMetionWebhookLink($post_id)
    {   
        return str_replace('{fb_post_id}', urlencode($post_id), static::BASE_URL.static::FB_POST_METION_HOOKED);
    }

    public static function getFBSearchPagesLink()
    {   
        return static::BASE_URL.static::FB_SEARCH_PAGES;
    }

    public static function getFBSearchPageInfoLink($fb_page_id)
    {   
        return str_replace('{fb_page_id}', urlencode($fb_page_id), static::BASE_URL.static::FB_SEARCH_PAGE_INFO);
    }

    public static function getPagePicture($fb_page_id)
    {   
        return str_replace('{fb_page_id}', urlencode($fb_page_id), static::BASE_URL.static::FB_PAGE_PICTURE);
    }

    
}
