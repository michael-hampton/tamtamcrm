<?php

namespace App\Notifications\Admin;

use App\Mail\Admin\ObjectSent;
use App\Models\AccountUser;
use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ReflectionException;

class EntitySentNotification extends Notification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    private $invitation;
    private $entity;

    /**
     * @var string
     */
    private string $entity_name;
    private $contact;

    /**
     * @var string
     */
    private string $message_type;

    /**
     * @var AccountUser
     */
    private AccountUser $account_user;

    /**
     * EntitySentNotification constructor.
     * @param Invitation $invitation
     * @param $entity_name
     * @param AccountUser $account_user
     * @param string $message_type
     */
    public function __construct(
        Invitation $invitation,
        $entity_name,
        AccountUser $account_user,
        string $message_type = ''
    ) {
        $this->invitation = $invitation;
        $this->entity_name = $entity_name;
        $this->entity = $invitation->{$entity_name};
        $this->contact = $invitation->contact;
        $this->message_type = $message_type;
        $this->account_user = $account_user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return !empty($this->message_type)
            ? [$this->message_type]
            : [
                $this->account_user->default_notification_type ?: 'mail'
            ];
    }

    /**
     * @param $notifiable
     * @return ObjectSent
     * @throws ReflectionException
     */
    public function toMail($notifiable)
    {
        return new ObjectSent($this->invitation, $notifiable);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [];
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)->from(trans('texts.from_slack'))->success()
                                 ->image($this->entity->account->present()->logo)
                                 ->content(
                                     trans(
                                         "texts.notification_{$this->entity_name}_sent_subject",
                                         $this->getDataArray()
                                     )
                                 );
    }

    private function getDataArray()
    {
        return [
            'total'    => $this->entity->getFormattedTotal(),
            'customer' => $this->contact->present()->name(),
            'invoice'  => $this->entity->getNumber(),
        ];
    }

}
