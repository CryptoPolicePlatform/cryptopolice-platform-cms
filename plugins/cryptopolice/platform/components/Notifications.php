<?php namespace CryptoPolice\Platform\Components;

use Cms\Classes\ComponentBase;
use CryptoPolice\Platform\Models\Notification;

class Notifications extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Notify',
            'description' => 'Users notifications'
        ];
    }

    public function onRun() {

        $notifications = Notification::get();
        $this->page['notifications'] = $notifications;
        $this->page['notifyCount'] = $notifications->count();
    }
}
