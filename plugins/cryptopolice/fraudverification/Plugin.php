<?php namespace CryptoPolice\FraudVerification;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
            'CryptoPolice\FraudVerification\components\Officer'         => 'Officer'
        ];
    }

    public function registerSettings()
    {

    }
}
