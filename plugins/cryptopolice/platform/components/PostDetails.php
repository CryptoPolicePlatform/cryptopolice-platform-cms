<?php namespace CryptoPolice\Platform\Components;

use DB;
use Auth;
use Flash;
use Validator;
use Cms\Classes\ComponentBase;
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

    public function setImagePath($diskName)
    {
        if($diskName) {
            return '\\storage\\app\\uploads\\public\\' . substr($diskName, 0, 3) . '\\' . substr($diskName, 3, 3) . '\\' . substr($diskName, 6, 3) . '\\' . $diskName;
        } else {
            return null;
        }
    }

    public function onRun()
    {

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
            ->leftJoin('system_files', function ($join) {
                $join->on('posts.id', '=', 'system_files.attachment_id')
                    ->where('system_files.attachment_type', 'CryptoPolice\Platform\Models\CommunityPost');
            })
            ->join('cryptopolice_platform_community_post_views as views', function ($join) {
                $join->on('posts.id', '=', 'views.post_id');
            })
            ->select(DB::raw("count(views.id) as views_count"), 'system_files.disk_name as post_img', 'posts.*', 'users.email', 'users.nickname')
            ->where('posts.slug', $this->param('slug'))
            ->where('posts.id', $this->param('id'))
            ->where('posts.status', 1)
            ->first();

        if (!$post->status) {
            return $this->controller->run('404');
        }
        
        $post->post_img = $this->setImagePath($post->post_img);
        $this->page['post'] = $post;
    }
}