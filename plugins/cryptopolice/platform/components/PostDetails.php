<?php namespace CryptoPolice\Platform\Components;

use DB;
use Auth;
use Flash;
use Validator;
use Cms\Classes\ComponentBase;

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
        return '..\\storage\\app\\uploads\\public\\' . substr($diskName, 0, 3) . '\\' . substr($diskName, 3, 3) . '\\' . substr($diskName, 6, 3) . '\\' . $diskName;
    }

    public function onRun()
    {

        $post = Db::table('cryptopolice_platform_community_posts as posts')
            ->join('users', 'posts.user_id', 'users.id')
            ->leftJoin('system_files', function ($join) {
                $join->on('posts.id', '=', 'system_files.attachment_id')
                    ->where('system_files.attachment_type', 'CryptoPolice\Platform\Models\CommunityPost');
            })
            ->select('system_files.disk_name as post_img', 'posts.*', 'users.email', 'users.nickname')
            ->where('posts.id', $this->param('id'))
            ->first();

        $post->post_img = $this->setImagePath($post->post_img);
        $this->page['post'] = $post;

    }
}