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

        $user = Auth::getUser();

        $counter = CommunityPost::
            select(
                'cryptopolice_platform_community_posts.*',
                DB::raw('count(*) as total_posts_count'),
                DB::raw('sum(status = 0) as pending_posts_count')
            )
            ->get();

        $totalPostsCount = $counter[0]->total_posts_count;
        $pendingPostsCount = $counter[0]->pending_posts_count;

        $users = User::
            select(
                'users.*',
                DB::raw('count(*) as users_count'),
                DB::raw('sum(is_activated = 1) as active_users')
            )
            ->get();

        $totalUserCount = $users[0]->users_count;
        $totalActiveUserCount = $users[0]->active_users;

        $this->page['active_user_count'] = $totalActiveUserCount;
        $this->page['active_user_percentage'] = ((100 / $totalUserCount) * $totalActiveUserCount) / 100;

        $this->page['post_count'] = $totalPostsCount;
        $this->page['post_pending'] = $pendingPostsCount ? $pendingPostsCount : 0;
        $this->page['post_published'] = $totalPostsCount - $pendingPostsCount;

        if ($totalPostsCount) {
            $this->page['post_pending_percentage'] = ((100 / $totalPostsCount) * $pendingPostsCount) / 100;
        }

        if ($totalPostsCount) {
            $this->page['post_count_percentage'] = ((100 / $totalPostsCount) * ($totalPostsCount - $pendingPostsCount)) / 100;
        }


        if ($user) {

            $usersPosts = CommunityPost::select(DB::raw("sum(user_id = " . $user->id . ") as users_posts"))->get();

            $postsCount = $usersPosts[0]->users_posts;
            $commentsCount = CommunityComment::where('user_id', $user->id)->count();

            $this->page['user_posts_count'] = $postsCount;
            $this->page['user_comments_count'] = $commentsCount;

            if($postsCount) {
                $this->page['user_comments_count_percentage'] = (100 / ($postsCount + $commentsCount) * $postsCount) / 100;
                $this->page['user_posts_count_percentage'] = (100 / ($postsCount + $commentsCount) * $commentsCount) / 100;
            }
            $this->page['user_activity'] = (($postsCount * 5) + $commentsCount) / 100;
        }
    }
}