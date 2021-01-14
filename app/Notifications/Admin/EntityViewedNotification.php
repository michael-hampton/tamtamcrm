<?php

namespace App\Notifications\Admin;

use App\Mail\Admin\ObjectViewed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class EntityViewedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     * @
     */

    private $invitation;

    /**
     * @var string
     */
    private string $entity_name;
    private $entity;
    private $contact;

    /**
     * @var string
     */
    private string $message_type;

    /**
     * EntityViewedNotification constructor.
     * @param $invitation
     * @param $entity_name
     * @param string $message_type
     */
    public function __construct($invitation, $entity_name, string $message_type = '')
    {
        $this->entity_name = $entity_name;
        $this->entity = $invitation->{$entity_name};
        $this->contact = $invitation->contact;
        $this->invitation = $invitation;
        $this->message_type = $message_type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $account_user = $this->invitation->account->account_users->where('user_id', '=', $notifiable->id)->first();

        return !empty($this->message_type)
            ? [$this->message_type]
            : [
                $account_user->default_notification_type
            ];
    }

    /**
     * @param $notifiable
     * @return ObjectViewed
     */
    public function toMail($notifiable)
    {
        return new ObjectViewed($this->invitation, $this->entity_name, $notifiable);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [//
        ];
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)->from(trans('texts.from_slack'))->success()
                                 ->content(
                                     $this->getMessage()
                                 );
    }

    private function getMessage()
    {
        return trans(
            "texts.notification_{$this->entity_name}_viewed_subject",
            [
                'customer'         => $this->contact->present()->name(),
                $this->entity_name => $this->entity->number,
            ]
        );
    }
}
