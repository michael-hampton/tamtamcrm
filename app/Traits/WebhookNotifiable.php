<?php

namespace App\Traits;

trait WebhookNotifiable
{
    /**
     * @return string
     */
    public function getSigningKey()
    {
        return $this->api_key;
    }

    /**
     * @return string
     */
    public function getWebhookUrl()
    {
        return $this->webhook_url;
    }
}