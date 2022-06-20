<?php

namespace App\Providers;

use App\Enums\TypeMessage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        \Carbon\Carbon::setlocale(config('app.locale'));
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            TypeMessage::FB_POSTS => 'App\FbPost',
            TypeMessage::FB_COMMENT => 'App\FbComment',
            TypeMessage::TWEETS => 'App\MediaTwitter',
            TypeMessage::IG_POSTS => 'App\Media',
            TypeMessage::IG_COMMENT => 'App\IgComment',
            TypeMessage::FB_PAGE_POST => 'App\FbPagePost',
            TypeMessage::FB_PAGE_POST_COMMENT => 'App\FbPagePostComment',
        ]);

    }
}
