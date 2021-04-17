<?php


namespace App\Notifications\PlanSubscription;


use App\Channels\WebhookChannel;
use App\Models\PlanSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpgradeSubscription extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var PlanSubscription
     */
    private PlanSubscription $plan_subscription;

    /**
     * @var string
     */
    private string $event_type;

    /**
     * CancelSubscription constructor.
     * @param PlanSubscription $plan_subscription
     * @param string $event_type
     */
    public function __construct(PlanSubscription $plan_subscription, string $event_type)
    {
        $this->plan_subscription = $plan_subscription;
        $this->event_type = $event_type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [WebhookChannel::class];
    }

    public function toWebhook($notifiable)
    {

        return [
            'event_type' => $this->event_type,
        ];
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
}