<?php

namespace App\Handlers;

use Spatie\WebhookClient\Jobs\ProcessWebhookJob;

class WebhookJobHandlerForPaynamicsPaybiz extends ProcessWebhookJob
{
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public int $timeout = 120;

    public function handle()
    {
        //You can perform an heavy logic here
        logger($this->webhookCall);
        sleep(10);
        logger("I am done");
    }
}
