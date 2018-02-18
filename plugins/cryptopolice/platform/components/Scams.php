<?php namespace CryptoPolice\Platform\Components;

use Cms\Classes\ComponentBase;
use CryptoPolice\Platform\Models\CommunityPost;

class Scams extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Scam list',
            'description' => 'Scam list'
        ];
    }

    public function onRun()
    {
        
    }
}