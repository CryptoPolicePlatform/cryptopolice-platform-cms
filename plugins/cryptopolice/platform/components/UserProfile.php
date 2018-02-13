<?php namespace CryptoPolice\Platform\Components;

use DB;
use RainLab\User\Models\User;
use Cms\Classes\ComponentBase;
use CryptoPolice\Platform\Models\CommunityPost;

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

        $this->page['post_pending_percentage'] = ((100 / $totalPostsCount) * $pendingPostsCount) / 100;
        $this->page['post_count_percentage'] = ((100 / $totalPostsCount) * ($totalPostsCount - $pendingPostsCount)) / 100;

    }
}