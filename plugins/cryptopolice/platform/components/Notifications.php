<?php namespace CryptoPolice\Platform\Components;

use CryptoPolice\Platform\Models\Notification;
use DB, Auth;
use Illuminate\Support\Carbon;
use Cms\Classes\ComponentBase;
use CryptoPolice\Platform\Models\UserNotification;

class Notifications extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Notify',
            'description' => 'Users notifications'
        ];
    }

    public function onRun()
    {
        if (Auth::check()) {
            $notifications = $this->getNotifyList();
            $this->page['notifications'] = $notifications;
            $this->page['notifyCount'] = $notifications->where('user_id', null)->where('notification_id', null)->count();
        }
    }

    public function getNotifyList()
    {

        return Notification::with('users_notifications')
            ->where('status', 1)
            ->where('announcement_at', '<', Carbon::now()->toDateTimeString())
            ->where('user_id', 0)
            ->orWhere('user_id', Auth::getUser()->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }


    public function onCheckNotification()
    {

        $user = Auth::getUser();

        $status = UserNotification::where('user_id', $user->id)
            ->where('notification_id', post('id'))
            ->get();

        if ($status->isEmpty()) {
            $model = new UserNotification();
            $model->notification_id = post('id');
            $model->user_id = $user->id;
            $model->save();
        }

        $this->page['notify'] = [
            'notification_id'   => $user->id,
            'user_id'           => post('id')
        ];
    }

    public function onBack()
    {
        $notifications = $this->getNotifyList();
        $this->page['notifications'] = $notifications;
    }
}
