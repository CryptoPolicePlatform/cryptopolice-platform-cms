<?php namespace CryptoPolice\Platform\Components;

use DB, Auth;
use RainLab\User\Models\User;
use Cms\Classes\ComponentBase;
use CryptoPolice\Platform\Models\CommunityPost;
use CryptoPolice\Platform\Models\CommunityComment;

class UserProfile extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Users list',
            'description' => 'Community Users List'
        ];
    }

    public function onRun()
    {

        $totalPostsCount = CommunityPost::count();
        $pendingPostsCount = CommunityPost::where('status', 0)->count();

        $this->page['user_count'] = User::count();
        $this->page['post_count'] = $totalPostsCount;
        $this->page['post_pending'] = $pendingPostsCount;
        $this->page['post_published'] = $totalPostsCount - $pendingPostsCount;

        if ($totalPostsCount) {
            $this->page['post_pending_percentage'] = ((100 / $totalPostsCount) * $pendingPostsCount) / 100;
        }

        if ($totalPostsCount) {
            $this->page['post_count_percentage'] = ((100 / $totalPostsCount) * ($totalPostsCount - $pendingPostsCount)) / 100;
        }

        $user = Auth::getUser();

        if($user) {
            $this->page['user_posts_count'] = CommunityPost::where('user_id', $user->id)->count();
            $this->page['user_comments_count'] = CommunityComment::where('user_id', $user->id)->count();
            $this->page['user_activity'] = (($this->page['user_posts_count'] * 5) + $this->page['user_comments_count']) / 100;
        }
    }
}