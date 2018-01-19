<?php namespace CryptoPolice\Academy\Components;

use Auth;
use Cms\Classes\ComponentBase;

class CustomUploader extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Custom Image Uploader',
            'description' => 'Custom Image Uploader'
        ];
    }


    function onInit()
    {

        $user = Auth::getUser();

        if ($user) {
            $component = $this->addComponent(
                'NetSTI\Uploader\Components\ImageUploader',
                'imageUploader',
                ['modelClass' => 'RainLab\User\Models\User', 'modelKeyColumn' => 'avatar', 'deferredBinding' => false]
            );

            $component->bindModel('avatar', $user);
        }
    }

}