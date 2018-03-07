<?php namespace CryptoPolice\Platform\Components;

use DB;
use RainLab\User\Models\User;
use Cms\Classes\ComponentBase;
use CryptoPolice\Platform\Classes\Helpers;

class Users extends ComponentBase
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

        $users = User::with('avatar', 'groups')
            ->whereNotNull('users.last_seen')
            ->orderBy('last_seen', 'desc')
            ->take(25)
            ->get();

        $this->page['users'] = $users;
    }
}