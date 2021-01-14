<?php

namespace App\Notifications\Admin;

use App\Mail\Admin\LeadCreated;
use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class NewLeadNotification extends Notification implements ShouldQueue
{
    use Queueable;


    /**
     * @var Lead
     */
    private Lead $lead;

    /**
     * @var string
     */
    private string $message_type;

    /**
     * NewLeadNotification constructor.
     * @param Lead $lead
     * @param string $message_type
     */
    public function __construct(Lead $lead, string $message_type = '')
    {
        $this->lead = $lead;
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
        return !empty($this->message_type)
            ? [$this->message_type]
            : [
                $notifiable->account_user()->default_notification_type
            ];
    }

    /**
     * @param $notifiable
     * @return LeadCreated
     */
    public function toMail($notifiable)
    {
        return new LeadCreated($this->lead, $notifiable);
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
        $logo = $this->lead->account->present()->logo();

        return (new SlackMessage)->success()
                                 ->from("System")->image($logo)->content(
                $this->getMessage()
            );
    }

    private function getMessage()
    {
        $this->subject = trans(
            'texts.notification_lead_subject',
            [
                'customer' => $this->lead->present()->name()
            ]
        );
    }

}
