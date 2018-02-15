<?php namespace CryptoPolice\Platform\Components;

use DB;
use Auth;
use Illuminate\Support\Carbon;
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
        if (Auth::check()) {
            $notifications = $this->getNotifyList();
            $this->page['notifications'] = $notifications;
            $this->page['notifyCount'] = $notifications->where('user_id', null)->where('notification_id', null)->count();
        }
    }

    public function getNotifyList()
    {

        return DB::table('cryptopolice_platform_notifications AS notify')
            ->leftJoin('cryptopolice_platform_users_notifications as users_notify', function ($join) {
                $join->on('notify.id', '=', 'users_notify.notification_id')
                    ->where('users_notify.user_id', Auth::getUser()->id);
            })
            ->where('notify.status', 1)
            ->where('notify.announcement_at', '<', Carbon::now()->toDateTimeString())
            // Get notification that defined for all users
            ->where('notify.user_id', 0)
            // Get notification that defined only for current user
            ->orWhere('notify.user_id', Auth::getUser()->id)
            ->select('notify.*', 'users_notify.user_id', 'users_notify.notification_id')
            ->orderBy('notify.created_at', 'desc')
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
