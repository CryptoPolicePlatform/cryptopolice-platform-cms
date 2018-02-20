<?php namespace CryptoPolice\Platform\Components;

use DB;
use Auth;
use Flash;
use Session;
use Request;
use Validator;
use Illuminate\Support\Carbon;
use Cms\Classes\ComponentBase;
use CryptoPolice\Academy\Models\Settings;
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
        return 'storage//app//uploads//public//' . substr($diskName, 0, 3) . '//' . substr($diskName, 3, 3) . '//' . substr($diskName, 6, 3) . '//' . $diskName;
    }

    public function onGetPosts()
    {

        $this->page['limit'] = true;
        $this->page['page_num'] = post('page') ? post('page') + 1 : 1;
        $this->page['search_data'] = post('search');

        // skip 100 records per page, for search 50
        $perPage = !empty(post('search')) ? 50 : 100;

        $skip = post('page') ? post('page') * $perPage : 0;

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
                    $query->orWhere('posts.post_description', 'like', '%' . post('search') . '%');
                }
            })
            ->orderBy('posts.pin', 'desc')
            ->orderBy('posts.created_at', 'desc')
            ->groupBy('posts.id')
            ->skip($skip)->take($perPage)
            ->get();

        if ($posts->isNotEmpty()) {

            // set path to users & post image
            foreach ($posts as $key => $value) {
                if ($value->users_image) {
                    $posts[$key]->users_image = $this->setImagePath($value->users_image);
                }
                if ($value->posts_image) {
                    $posts[$key]->posts_image = $this->setImagePath($value->posts_image);
                }

                // set status
                $posts[$key]->status = $this->setStatus($value->created_at, $value->views_count);

                // set shares links
                $posts[$key]->facebook = $this->setFacebookShare();
                $posts[$key]->twitter = $this->setTwitterShare($value->post_description);

            }
            $this->page['posts'] = $posts;

        } else {

            // if empty query collection, disable load more form
            $this->page['limit'] = false;
        }
    }

    public function setStatus($createdAt, $views)
    {

        $hours = Carbon::now()->diffInHours(Carbon::parse($createdAt));
        if ($hours > Settings::get('hot_post_min_hours') && $hours < Settings::get('hot_post_max_hours') && $views > Settings::get('hot_post_views'))
            return 3;
        if ($hours > Settings::get('med_post_min_hours') && $hours < Settings::get('med_post_max_hours') && $views > Settings::get('med_post_views'))
            return 2;
        if ($hours > Settings::get('new_post_min_hours') && $hours < Settings::get('new_post_max_hours') && $views > Settings::get('new_post_views'))
            return 1;
        return 0;
    }


    public function onAddPost()
    {

        Recaptcha::verifyCaptcha();

        if (input('_token') == Session::token()) {

            $user = Auth::getUser();

            $previousPost = CommunityPost::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->first();

            $minutes = $this->compareDates($previousPost->created_at);
            if ($minutes < 10) {
                Flash::error('You will be able to post after ' . (10 - $minutes) . ' min(s)');
                return false;
            }

            $html = Markdown::parse(strip_tags(input('description')));

            $post = new CommunityPost;
            $post->post_title = input('title');
            $post->post_description = $html;
            $post->user_id = $user->id;
            $post->save();

            Flash::success('Post has been successfully added');
            return redirect()->back();

        }
    }

    public function compareDates($date)
    {
        return Carbon::now()->diffInMinutes(Carbon::parse($date));
    }

    public function setFacebookShare()
    {
        return 'https://www.facebook.com/sharer/sharer.php?' . http_build_query(['u' => $this->currentPageUrl()]);
    }

    public function setTwitterShare($text)
    {
        return 'https://twitter.com/share?' . http_build_query(['url' => $this->currentPageUrl(), 'text' => $text]);
    }
}