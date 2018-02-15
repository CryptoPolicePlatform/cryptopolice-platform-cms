<?php namespace CryptoPolice\Platform\Components;

use DB;
use Auth;
use Flash;
use Session;
use Request;
use Validator;
use Illuminate\Support\Carbon;
use Cms\Classes\ComponentBase;
use October\Rain\Support\Facades\Markdown;
use CryptoPolice\Academy\Components\Recaptcha;
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

    public function onRun()
    {
        $this->onGetPosts();
    }

    public function setImagePath($diskName)
    {
        return 'storage\\app\\uploads\\public\\' . substr($diskName, 0, 3) . '\\' . substr($diskName, 3, 3) . '\\' . substr($diskName, 6, 3) . '\\' . $diskName;
    }

    public function onGetPosts()
    {

        if(post('search')) {
            if (input('_token') != Session::token()) {
                return;
            }
        }

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
            ->leftJoin('cryptopolice_platform_community_post_views as views', function ($join) {
                $join->on('posts.id', '=', 'views.post_id');
            })
            ->select(DB::raw('count(views.id) as views_count'), 'users_files.disk_name as users_image', 'posts_files.disk_name as posts_image', 'posts.*')
            ->where('posts.status', 1)
            ->Where(function ($query) {
                if (!empty(post('search'))) {
                    $query->where('posts.post_title', 'like', '%' . post('search') . '%');
                }
            })
            ->orderBy('posts.pin', 'desc')
            ->orderBy('posts.created_at', 'desc')
            ->groupBy('posts.id')
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

        if (input('_token') == Session::token()) {

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

                $getLastPost = CommunityPost::where('user_id', $user->id)->orderBy('created_at', 'desc')->first();


                if (isset($getLastPost->created_at) && !empty($getLastPost->created_at)) {
                    $diffInMinutes = Carbon::now()->diffInMinutes(Carbon::parse($getLastPost->created_at));
                    if ($diffInMinutes < 10) {
                        Flash::error('You will be able to post after ' . (10 - $diffInMinutes) . ' min(s)');
                        return;
                    }
                }

                $html = Markdown::parse(strip_tags(input('description')));

                $post = new CommunityPost;
                $post->post_title = input('title');
                $post->post_description = $html;
                $post->user_id = $user->id;
                $post->save(null, post('_session_key'));

                Flash::success('Post has been successfully added');
                return redirect()->back();
            }
        }
    }
}