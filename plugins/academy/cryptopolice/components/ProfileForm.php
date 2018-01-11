<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 18.11.1
 * Time: 11:44
 */

namespace Academy\CryptoPolice\Components;

use Cms\Classes\ComponentBase;

class ProfileForm extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Profile Form',
            'description' => 'Users profile form'
        ];
    }

    function onRun() {

    }

}