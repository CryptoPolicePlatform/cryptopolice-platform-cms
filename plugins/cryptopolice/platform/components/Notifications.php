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

        return Notification::leftJoin('cryptopolice_platform_users_notifications as users_notifications', function ($join) {
                $user = Auth::getUser();
                $join->on('cryptopolice_platform_notifications.id', '=', 'users_notifications.notification_id')
                    ->where('users_notifications.user_id', $user->id);
            })
            ->where('status', 1)
            ->where('announcement_at', '<', Carbon::now()->toDateTimeString())
            ->orWhere('announcement_at', null)
            ->orderBy('cryptopolice_platform_notifications.created_at', 'desc')
            ->select('cryptopolice_platform_notifications.*', 'users_notifications.user_id', 'users_notifications.notification_id')
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
