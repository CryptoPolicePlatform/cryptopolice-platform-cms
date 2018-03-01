<?php namespace CryptoPolice\Platform\Components;

use DB, Auth, Flash, Validator, Session;
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
        $this->setPageVisit();
        $this->getPost();
    }

    public function setPageVisit() {

        $watched = CommunityPostViews::where('session_id', Session::getId())
            ->where('post_id', $this->param('id'))
            ->get();

        if ($watched->isEmpty()) {
            CommunityPostViews::insert([
                'session_id' => Session::getId(),
                'post_id' => $this->param('id')
            ]);
        }
    }

    public function getPost() {

        $helper = new Helpers();

        $post = CommunityPost::with('post_image','user.avatar','user','comments')
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

        $post->comments_count = $post->comments->count();

        if (!$post->status) {
            return $this->controller->run('404');
        }

        $helper = new Helpers();
        $post->status = $helper->setStatus($post->created_at, $post->views_count, $post->comment_count);

        $this->page['post'] = $post;
    }
}