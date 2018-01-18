<?php namespace CryptoPolice\CryptoPolice\Components;

use Flash;
use Cms\Classes\ComponentBase;

class UsersBounties extends ComponentBase
{
    public $usersBountyList;


    public function componentDetails()
    {
        return [
            'name' => 'Users Bounties',
            'description' => 'Users Bounties List'
        ];
    }

    public function onRun()
    {
     
    }
}
