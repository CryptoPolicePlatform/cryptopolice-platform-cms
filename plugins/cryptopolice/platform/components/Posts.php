<?php namespace CryptoPolice\Platform\Components;

use Input;
use DB;
use Auth;
use Flash;
use Session;
use Request;
use Validator;
use Illuminate\Support\Carbon;
use Cms\Classes\ComponentBase;
use CryptoPolice\Academy\Models\Settings;
use CryptoPolice\Platform\Classes\Helpers;
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

    public function onGetPosts()
    {

        $this->page['limit'] = true;
        $this->page['page_num'] = post('page') ? post('page') + 1 : 1;
        $this->page['search_data'] = post('search');

        // skip 100 records per page, for search 100
        $perPage = 100;

        $skip = post('page') ? post('page') * $perPage : 0;

        $posts = CommunityPost::with('post_image', 'user.avatar','comments.user.avatar', 'comments', 'views')
            ->select('cryptopolice_platform_community_posts.*')
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->Where(function ($query) {
                if (!empty(post('search'))) {
                    $query->where('post_title', 'like', '%' . post('search') . '%');
                    $query->orWhere('post_description', 'like', '%' . post('search') . '%');
                }
            })
            ->orderBy('pin', 'desc')
            ->orderBy('created_at', 'desc')
            ->groupBy('cryptopolice_platform_community_posts.id')
            ->skip($skip)->take($perPage)
            ->get();



        if ($posts->isNotEmpty()) {

            $helper = new Helpers();
            $this->page['limit']  = $posts->count() > $perPage ? true : false;

            foreach ($posts as $key => $value) {

                $posts[$key]->comment_count = $value->comments->count();
                $posts[$key]->views_count = $value->views->count();
                // set status
                $posts[$key]->status = $helper->setStatus($value->created_at, $value->views_count, $value->comment_count);

                $helper = new Helpers();
                // set shares links
                $posts[$key]->twitter   = $helper->setTwitterShare($value->post_description);
                $posts[$key]->reddit    = $helper->setRedditShare($value->post_title);
                $posts[$key]->facebook  = $helper->setFacebookShare();

            }
            $this->page['posts'] = $posts;

        } else {

            // if empty query collection, disable load more form
            $this->page['limit'] = false;
        }
    }

    public function onAddPost()
    {

        Recaptcha::verifyCaptcha();
        
        if (input('_token') == Session::token()) {

            $user = Auth::getUser();

            $previousPost = CommunityPost::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            if($previousPost->isNotEmpty()) {
                $minutes = $this->compareDates($previousPost[0]->created_at);
                if ($minutes < 10) {
                    Flash::error('You will be able to post after ' . (10 - $minutes) . ' min(s)');
                    return false;
                }
            }

            $helper = new Helpers();
            $description    = Markdown::parse(strip_tags(input('description')));
            $title          = strip_tags(input('title'));

            if ($helper->checkLinks($description)) {
                Flash::error('Links are not allowed');
            }
//            else if (!empty($title) && preg_match('/^[A-Za-z0-9_~\-!=<>|:;?"+@#\$%\^&\*\(\)]+$/', $title)) {
//                Flash::error('Not allows the use emoji in the title');

//            }
            else {

                $post = new CommunityPost;
                $post->post_title = $title;
                $post->post_description = $description;
                $post->user_id = $user->id;
                $post->save(null, post('_session_key'));

                Flash::success('Post has been successfully added');
                return redirect()->back();
            }
        }
    }

    public function compareDates($date)
    {
        return Carbon::now()->diffInMinutes(Carbon::parse($date));
    }

}