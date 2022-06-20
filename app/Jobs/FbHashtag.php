<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Enums\SocialMedia;
use App\FbPagePost;
use App\FbPagePostComment;
use App\Hashtag;

class FbHashtag implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 2000;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        set_time_limit(0);
        $hashtags_ativas = Hashtag::where('social_media_id', SocialMedia::FACEBOOK)
                        ->where('is_active',true)->get();

        foreach ($hashtags_ativas as $hashtag) {
            $last = $hashtag->pagePosts()->latest('created_at')->first();
            $last_comment = $hashtag->pagePostsComments()->latest('created_at')->first();
            $posts = FbPagePost::select('id')->where(function ($query) use ($hashtag) {
                    $query->where('message', 'ilike', '% #'.strtolower($hashtag->hashtag).' %')
                        ->orWhere('message', 'ilike', '%#'.strtolower($hashtag->hashtag).' %')
                        ->orWhere('message', 'ilike', '% #'.strtolower($hashtag->hashtag).'%');
                    })
                    ->when($last, function ($q) use ($last){
                        return $q->where('updated_time', '>=', $last->created_at->subDay()->toDateString());
                    })
                    ->get();
            $hashtag->pagePosts()->syncWithoutDetaching($posts->pluck('id')->toArray());

            $comments = FbPagePostComment::select('id')->where(function ($query) use ($hashtag) {
                    $query->where('text', 'ilike', '% #'.strtolower($hashtag->hashtag).' %')
                        ->orWhere('text', 'ilike', '%#'.strtolower($hashtag->hashtag).' %')
                        ->orWhere('text', 'ilike', '% #'.strtolower($hashtag->hashtag).'%');
                    })
                    ->when($last_comment, function ($q) use ($last_comment){
                        return $q->where('created_time', '>=', $last_comment->created_at->subDay()->toDateString());
                    })
                    ->get();
            $hashtag->pagePostsComments()->syncWithoutDetaching($comments->pluck('id')->toArray());

        }

    }
}
