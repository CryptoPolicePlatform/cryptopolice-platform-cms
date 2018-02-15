<?php namespace CryptoPolice\Platform\Components;

use DB;
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

    public function setPath($diskName)
    {
        return '\\storage\\app\\uploads\\public\\' . substr($diskName, 0, 3) . '\\' . substr($diskName, 3, 3) . '\\' . substr($diskName, 6, 3) . '\\' . $diskName;
    }

    public function onRun()
    {

        $users = Db::table('users')
            ->leftJoin('system_files', function ($join) {
                $join->on('users.id', '=', 'system_files.attachment_id')
                    ->where('system_files.attachment_type', 'RainLab\User\Models\User');
            })
            ->select('system_files.disk_name as user_image', 'users.*')
            ->orderBy('last_seen', 'desc')
            ->get();

        foreach ($users as $key => $value) {

            if ($value->user_image) {
                $users[$key]->user_image = $this->setPath($value->user_image);
            }
        }

        $this->page['users'] = $users;
    }
}