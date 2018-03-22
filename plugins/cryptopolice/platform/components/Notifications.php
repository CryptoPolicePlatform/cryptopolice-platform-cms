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

            foreach ($notifications as $notify) {
                if (!isset($notify->users_notifications[0]->user_id) && !isset($notify->users_notifications[0]->notification_id)) {
                    $this->page['notifyCount'] += 1;
                }
            }
        }
    }

    public function getNotifyList()
    {

        $user = Auth::getUser();
        return Notification::with(['users_notifications' => function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }])
            ->where('user_id', $user->id)
            ->orWhere('user_id', 0)
            ->where('announcement_at', '<', Carbon::now()->toDateTimeString())
            ->where('created_at', '>', $user->created_at->toDateTimeString())
            ->where('status', 1)
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

        $this->page['notification'] = [
            'notification_id' => $user->id,
            'user_id' => post('id')
        ];
    }

    public function onBack()
    {
        $notifications = $this->getNotifyList();
        $this->page['notifications'] = $notifications;
    }
}
