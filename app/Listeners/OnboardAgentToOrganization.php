<?php

namespace App\Listeners;

use App\Notifications\OnboardedAgentNotification;
use MOIREI\Vouchers\Events\VoucherRedeemed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;

class OnboardAgentToOrganization
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param VoucherRedeemed $event
     * @return void
     */
    public function handle(VoucherRedeemed $event)
    {
        if ($event->redeemer instanceof User) {
            $agent = $event->redeemer;
            $organization = $event->voucher->campaign->repository->organization;
            $organization->users()->attach($agent);
            $agent->notify(new OnboardedAgentNotification($event->voucher));
        }
    }
}
