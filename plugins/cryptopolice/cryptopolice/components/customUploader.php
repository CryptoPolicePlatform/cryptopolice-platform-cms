<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 18.12.1
 * Time: 12:33
 */

namespace CryptoPolice\CryptoPolice\Components;

use Auth;
use Cms\Classes\ComponentBase;
// TODO:: nazvanie classov s bolsoj bukvi
class customUploader extends ComponentBase
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
        if($user){
            $component = $this->addComponent(
                'NetSTI\Uploader\Components\ImageUploader',
                'imageUploader',
                ['modelClass'=>'RainLab\User\Models\User','modelKeyColumn'=>'avatar', 'deferredBinding' => false]
            );

            $component->bindModel('avatar', $user);
        }
    }

}