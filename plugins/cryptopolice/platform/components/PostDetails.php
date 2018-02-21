<?php namespace CryptoPolice\Platform\Components;

use DB, Auth, Flash, Validator;
use Cms\Classes\ComponentBase;
use CryptoPolice\Platform\Classes\Helpers;
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

        $post = Db::table('cryptopolice_platform_community_posts as posts')
            ->join('users', 'posts.user_id', 'users.id')
            ->leftJoin('system_files as user_file', function ($join) {
                $join->on('users.id', '=', 'user_file.attachment_id')
                    ->where('user_file.attachment_type', 'RainLab\User\Models\User');
            })
            ->leftJoin('system_files as post_file', function ($join) {
                $join->on('posts.id', '=', 'post_file.attachment_id')
                    ->where('post_file.attachment_type', 'CryptoPolice\Platform\Models\CommunityPost');
            })
            ->join('cryptopolice_platform_community_post_views as views', function ($join) {
                $join->on('posts.id', '=', 'views.post_id');
            })
            ->select(DB::raw("count(views.id) as views_count"), 'post_file.disk_name as post_img', 'user_file.disk_name as user_img', 'posts.*', 'users.email', 'users.nickname')
            ->where('posts.slug', $this->param('slug'))
            ->where('posts.id', $this->param('id'))
            ->where('posts.status', 1)
            ->first();

        $post->facebook = $helper->setFacebookShare();
        $post->twitter  = $helper->setTwitterShare($post->post_title);
        $post->reddit   = $helper->setRedditShare($post->post_title);

        if (!$post->status) {
            return $this->controller->run('404');
        }

        $post->post_img = $helper->setImagePath($post->post_img);
        $post->user_img = $helper->setImagePath($post->user_img);

        $this->page['post'] = $post;
    }
}