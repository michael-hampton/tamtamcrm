<?php

namespace App\Listeners\Entity;

use App\Models\Notification;
use App\Notifications\Admin\EntityViewedNotification;
use App\Traits\Notifications\UserNotifies;
use Illuminate\Contracts\Queue\ShouldQueue;

class EntityViewedListener implements ShouldQueue
{
    use UserNotifies;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        $entity_name = $event->entity;
        $invitation = $event->invitation;

        $notification = new EntityViewedNotification($invitation, $entity_name);

        $this->send($notification, $invitation, $entity_name);

        if (isset($invitation->account->slack_webhook_url)) {
            $notification->method = ['slack'];

            Notification::route('slack', $invitation->account->slack_webhook_url)->notify($notification);
        }
    }

    /**
     * @param $notification
     * @param $invitation
     * @param $entity_name
     * @return bool
     */
    private function send($notification, $invitation, $entity_name)
    {
        $entity_viewed = "{$entity_name}_viewed";

        foreach ($invitation->account->account_users as $account_user) {
            $notification->method = $this->findUserNotificationTypesByInvitation(
                $invitation,
                $account_user,
                $entity_name,
                ['all_notifications', $entity_viewed]
            );

            $account_user->user->notify($notification);
        }

        return true;
    }

}
