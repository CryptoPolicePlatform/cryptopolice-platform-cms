<?php namespace cryptopolice\airdrop;

use RainLab\User\Models\User as UserModel;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
            'CryptoPolice\Airdrop\Components\UsersAirdrop' => 'Airdrop',
        ];

    }

    public function boot()
    {
        $this->extendUserModel();
    }

    protected function extendUserModel()
    {
        UserModel::extend(function ($model) {

            $model->hasOne = [

                'airDropRegistration' => [
                    'CryptoPolice\Airdrop\Models\AirdropRegistration',
                    'table' => 'cryptopolice_airdrop_user_registration',
                    'key' => 'user_id'
                ]
            ];
        });
    }
}
