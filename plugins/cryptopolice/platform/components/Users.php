<?php namespace CryptoPolice\Platform\Components;

use DB;
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

        $users = Db::table('users')
            ->leftJoin('system_files', function ($join) {
                $join->on('users.id', '=', 'system_files.attachment_id')
                    ->where('system_files.attachment_type', 'RainLab\User\Models\User');
            })
            ->select('system_files.disk_name as user_image', 'users.*')
            ->whereNotNull('users.last_seen')
            ->orderBy('last_seen', 'desc')
            ->get();

        $helper = new Helpers();
        foreach ($users as $key => $value) {
            if ($value->user_image) {
                $users[$key]->user_image = $helper->setImagePath($value->user_image);
            }
        }

        $this->page['users'] = $users;
    }
}