<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use ReflectionClass;

class LogSentMessage implements ShouldQueue
{

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($message)
    {
        //https://laracasts.com/discuss/channels/general-discussion/capturing-smtp-message-id?page=1

        if (!empty($message->message->entity)) {
            $entity_class = (new ReflectionClass($message->message->entity))->getShortName();

            $message_id = env('MAIL_MAILER') === 'postmark' ? $message->message->getHeaders()->get(
                'x-pm-message-id'
            ) : $message->message->getId();

            if ($entity_class === 'Invitation') {
                $message->message->entity->email_id = $message_id;
                $message->message->entity->save();
            }
        }
    }
}
