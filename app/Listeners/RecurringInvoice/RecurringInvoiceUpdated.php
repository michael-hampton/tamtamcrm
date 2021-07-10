<?php

namespace App\Listeners\RecurringInvoice;

use App\Services\Pdf\GeneratePdf;
use App\Factory\NotificationFactory;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecurringInvoiceUpdated implements ShouldQueue
{
    /**
     * @var NotificationRepository
     */
    protected NotificationRepository $notification_repo;

    /**
     * Create the event listener.
     *
     * @param NotificationRepository $notification_repo
     */
    public function __construct(NotificationRepository $notification_repo)
    {
        $this->notification_repo = $notification_repo;
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        $data = [
            'id'          => $event->recurring_invoice->id,
            'customer_id' => $event->recurring_invoice->customer_id,
            'message'     => 'A recurring invoice was updated'
        ];

        $fields = [
            'notifiable_id'   => $event->recurring_invoice->user_id,
            'account_id'      => $event->recurring_invoice->account_id,
            'notifiable_type' => get_class($event->recurring_invoice),
            'type'            => get_class($this),
            'data'            => json_encode($data),
            'action'          => 'updated'
        ];

        $notification = NotificationFactory::create(
            $event->recurring_invoice->account_id,
            $event->recurring_invoice->user_id
        );
        $notification->entity_id = $event->recurring_invoice->id;
        $this->notification_repo->save($notification, $fields);

        // regenerate pdf
        (new GeneratePdf($event->recurring_invoice))->execute(null, true);
    }
}
