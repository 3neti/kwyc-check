<?php

namespace App\Handlers;

use Spatie\WebhookClient\Jobs\ProcessWebhookJob;
use Illuminate\Support\Arr;
use App\Actions\TopupUser;
use App\Models\User;


class WebhookJobHandlerForPaynamicsPaybiz extends ProcessWebhookJob
{
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public int $timeout = 120;

    protected User $user;

    public function handle()
    {
        $amount = Arr::get($this->getPayloadCustomerInfo(), 'amount');
        $this->user = User::eurekaPersist($this->getPayloadCustomerInfo());

        logger(TopupUser::run($this->user, $amount));
    }

    public function getUser(): User
    {
        return $this->user;
    }

    protected function getPayloadCustomerInfo(): array
    {
        return Arr::get($this->webhookCall, 'payload.customer_info');
    }
}
