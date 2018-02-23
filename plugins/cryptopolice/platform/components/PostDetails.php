<?php namespace CryptoPolice\Platform\Components;

use DB, Auth, Flash, Validator;
use Cms\Classes\ComponentBase;
use CryptoPolice\Platform\Classes\Helpers;
use CryptoPolice\Platform\Models\CommunityPost;
use CryptoPolice\Platform\Models\CommunityPostViews;

class PostDetails extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Posts list',
            'description' => 'Community Posts List'
        ];
    }

    public function onRun()
    {

        $helper = new Helpers();

        if (Auth::check()) {

            $user = Auth::getUser();
            $watched = CommunityPostViews::where('user_id', $user->id)
                ->where('post_id', $this->param('id'))
                ->get();

            if ($watched->isEmpty()) {
                CommunityPostViews::insert([
                    'user_id' => $user->id,
                    'post_id' => $this->param('id')
                ]);
            }
        }

        $post = CommunityPost::with('post_image','user.avatar','user')
            ->join('cryptopolice_platform_community_post_views as views', function ($join) {
                $join->on('cryptopolice_platform_community_posts.id', '=', 'views.post_id');
            })
            ->select(DB::raw('count(views.id) as views_count'), 'cryptopolice_platform_community_posts.*')
            ->where('slug', $this->param('slug'))
            ->where('cryptopolice_platform_community_posts.id', $this->param('id'))
            ->where('status', 1)
            ->first();
        $post->facebook = $helper->setFacebookShare();
        $post->twitter  = $helper->setTwitterShare($post->post_title);
        $post->reddit   = $helper->setRedditShare($post->post_title);

        if (!$post->status) {
            return $this->controller->run('404');
        }

        $this->page['post'] = $post;
    }
}