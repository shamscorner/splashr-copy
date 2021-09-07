<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\UserRegisteredNotification;

class SendUserRegisteredNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        // TODO: get the managers who will get the notification
        $managers = User::where('id', function ($query) {
            $query->select('user_id')->from('managers')->whereColumn('user_id', 'users.id')->limit(1);
        })->get();
        // send the notification to each manager
        Notification::send($managers, new UserRegisteredNotification($event->user));
    }
}
