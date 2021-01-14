<?php

namespace App\Jobs\Subscription;

use App\Models\Subscription;
use App\Repositories\SubscriptionRepository;
use App\Traits\EntityDataBuilder;
use GuzzleHttp\Client;
use Illuminate\Foundation\Bus\Dispatchable;

class SendSubscription
{
    use EntityDataBuilder;
    use Dispatchable;

    protected $event;
    protected $entity;
    protected array $data = [];

    /**
     * SendSubscription constructor.
     * @param $entity
     * @param $event
     * @param array|null $data
     */
    public function __construct($entity, $event, array $data = null)
    {
        $this->event = $event;
        $this->entity = $entity;
    }

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle()
    {
        $subscription = (new SubscriptionRepository(new Subscription))->findSubscriptionByEvent(
            $this->event,
            $this->entity->account
        );


        if (empty($subscription) || $subscription->count() === 0) {
            return true;
        }

        $data = !empty($this->data) ? $this->data : $this->buildEntityData($this->entity);

        if (empty($data)) {
            return false;
        }

        $this->sendData($data, $subscription);

        return true;
    }

    private function sendData(array $data, Subscription $subscription)
    {
        return true;

        $client = new Client();
        $response = $client->request(
            'POST',
            $subscription->target_url,
            [
                'headers'     => [
                    'Authorization' => 'Bearer ' . 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC90YXNrbWFuMi5kZXZlbG9wXC9hcGlcL2xvZ2luIiwiaWF0IjoxNTk4MjkwMzE1LCJleHAiOjE1OTgzMDExMTUsIm5iZiI6MTU5ODI5MDMxNSwianRpIjoiMnBDSTB3Q2w0WGpIQ0NtaCIsInN1YiI6NSwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.E20vNJAUQFktJeEbV09s3o643G7rDCQ5a_FJ5ZzeU6k',
                    'Accept'        => 'application/json',
                ],
                'form_params' => $data
            ]
        );

        $response = json_decode($response->getBody(), true);

        echo '<pre>';
        print_r($response);
        die;
    }
}
