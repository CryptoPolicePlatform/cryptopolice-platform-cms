<?php namespace CryptoPolice\Platform\Components;

use Cms\Classes\ComponentBase;
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

         $this->page['posts'] = CommunityPost::where('status', 1)->orderBy('created_at', 'asc')
              ->get();
    }

}