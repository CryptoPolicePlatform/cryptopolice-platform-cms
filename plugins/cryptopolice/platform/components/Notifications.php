<?php namespace CryptoPolice\Platform\Components;

use Auth;
use Cms\Classes\ComponentBase;
use CryptoPolice\Platform\Models\Notification;
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

        $notifications = $this->getNotifyList();
        $this->page['notifications'] = $notifications;
        $this->page['notifyCount'] = $notifications->where('user_id', null)->where('notification_id', null)->count();
    }

    public function getNotifyList()
    {
        return Notification::where('status', 1)

            ->leftJoin('cryptopolice_platform_users_notifications as users_notifications', function ($join) {

                $user = Auth::getUser();

                $join->on('cryptopolice_platform_notifications.id', '=', 'users_notifications.notification_id')
                    ->where('users_notifications.user_id', $user->id);
            })

            ->orderBy('cryptopolice_platform_notifications.created_at', 'desc')
            ->select('cryptopolice_platform_notifications.*', 'users_notifications.user_id', 'users_notifications.notification_id')
            ->get();
    }

    public function onCheckNotification()
    {

        $user = Auth::getUser();
        $this->page['notification'] = Notification::where('id', post('id'))->where('status', 1)->first();
        $status = UserNotification::where('user_id', $user->id)->where('notification_id', post('id'))->get();

        if ($status->isEmpty()) {
            $comment = new UserNotification();
            $comment->notification_id = post('id');
            $comment->user_id = $user->id;
            $comment->save();
        } else {
            return;
        }
    }

    public function onBack()
    {
        $notifications = $this->getNotifyList();
        $this->page['notifications'] = $notifications;
    }
}
