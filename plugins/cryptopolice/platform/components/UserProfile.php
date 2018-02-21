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

        $totalUserCount = User::count();
        $totalActiveUserCount = User::where('is_activated', 1)->count();

        $this->page['active_user_count'] = $totalUserCount;
        $this->page['active_user_percentage'] = ((100 / $totalUserCount) * $totalActiveUserCount) / 100;

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

        if ($user) {

            $postsCount = CommunityPost::where('user_id', $user->id)->count();
            $commentsCount = CommunityComment::where('user_id', $user->id)->count();

            $this->page['user_posts_count'] = $postsCount;
            $this->page['user_comments_count'] = $commentsCount;

            $this->page['user_comments_count_percentage'] = (100 / ($postsCount + $commentsCount) * $postsCount) / 100;
            $this->page['user_posts_count_percentage'] = (100 / ($postsCount + $commentsCount) * $commentsCount) / 100;

            $this->page['user_activity'] = (($postsCount * 5) + $commentsCount) / 100;
        }
    }
}