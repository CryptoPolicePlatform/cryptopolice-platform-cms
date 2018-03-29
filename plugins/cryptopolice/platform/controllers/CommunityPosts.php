<?php namespace CryptoPolice\Platform\Controllers;

use Carbon\Carbon;
use Backend\Classes\Controller;
use BackendMenu;
use CryptoPolice\Platform\Models\Notification;

class CommunityPosts extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend\Behaviors\ReorderController'
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $reorderConfig = 'config_reorder.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('CryptoPolice.Platform', 'menu', 'side-menu-item');
    }

    public function formAfterSave($model)
    {
        if ($model->notify) {
            $this->addUNotification($model);
        }
    }

    public function addUNotification($model)
    {
        $notify = new Notification();
        $notify->title = $model->post_title;
        $notify->description = $model->post_description;
        $notify->announcement_at = Carbon::now();
        $notify->user_id = 0;
        $notify->save();
    }
}
