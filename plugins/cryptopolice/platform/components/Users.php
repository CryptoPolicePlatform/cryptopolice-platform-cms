<?php namespace CryptoPolice\Platform\Components;

use RainLab\User\Models\User;
use Cms\Classes\ComponentBase;

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
        $this->page['users'] = User::orderBy('last_seen', 'asc')->get();
    }

}