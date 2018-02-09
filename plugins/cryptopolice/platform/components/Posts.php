<?php namespace CryptoPolice\Platform\Components;

use DB;
use Auth;
use Flash;
use Validator;
use Cms\Classes\ComponentBase;
use CryptoPolice\Platform\Models\CommunityPost;

class Posts extends ComponentBase
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
        return 'storage\\app\\uploads\\public\\' . substr($diskName, 0, 3) . '\\' . substr($diskName, 3, 3) . '\\' . substr($diskName, 6, 3) . '\\' . $diskName;
    }

    public function onRun()
    {

        $posts = Db::table('cryptopolice_platform_community_posts as posts')
            ->join('users', 'posts.user_id', 'users.id')
            ->leftJoin('system_files as users_files', function ($join) {
                $join->on('users.id', '=', 'users_files.attachment_id')
                    ->where('users_files.attachment_type', 'RainLab\User\Models\User');
            })
            ->leftJoin('system_files as posts_files', function ($join) {
                $join->on('posts.id', '=', 'posts_files.attachment_id')
                    ->where('posts_files.attachment_type', 'CryptoPolice\Platform\Models\CommunityPost');
            })
            ->select('users_files.disk_name as users_image', 'posts_files.disk_name as posts_image', 'posts.*')
            ->where('posts.status', 1)
            ->orderBy('posts.created_at', 'desc')
            ->get();

        foreach ($posts as $key => $value) {
            if ($value->users_image) {
                $posts[$key]->users_image = $this->setImagePath($value->users_image);
            }
            if ($value->posts_image) {
                $posts[$key]->posts_image = $this->setImagePath($value->posts_image);
            }
        }

        $this->page['posts'] = $posts;
    }

    public function onAddPost()
    {

        $user = Auth::getUser();

        $rules = [
            'title' => 'required|min:0|max:255',
            'description' => 'required|min:0|max:10000'
        ];

        $validator = Validator::make(input(), $rules);

        if ($validator->fails()) {
            $messages = $validator->messages();
            foreach ($messages->all() as $message) {
                Flash::error($message);
            }
        } else {

            $post = new CommunityPost;
            $post->post_title = input('title');
            $post->post_description = input('description');
            $post->user_id = $user->id;
            $post->save(null, post('_session_key'));

            Flash::success('You\'re nickname has been updated');
            return redirect()->back();
        }
    }
}