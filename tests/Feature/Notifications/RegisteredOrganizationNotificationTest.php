<?php

namespace Tests\Feature\Notifications;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Notifications\RegisteredOrganizationNotification;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use App\Models\Campaign;
use App\Models\User;
use Tests\TestCase;

class RegisteredOrganizationNotificationTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function send_campaign_notification_accepts_campaign()
    {
        /*** arrange ***/
        Notification::fake();
        $mobile = '09171234567'; //TODO: provider a more robust PH mobile faker
        $user = User::factory()->create(['mobile' => $mobile]);
        $campaign = Campaign::factory()->create();
        $voucher = $campaign->createVoucher();

        /*** act ***/
        $user->notify(new RegisteredOrganizationNotification($voucher));

        /*** assert ***/
        Notification::assertSentTo($user, function(RegisteredOrganizationNotification $notification) use ($user) {
            $register_user_message = $notification->getContent($user);

            return $register_user_message == trans('domain.org-campaign', [
                    'org' => $notification->getCampaign()->repository->organization->name,
                    'url' => route('create-recruit', [
                        'voucher' => $notification->voucher->code
                    ])
                ]);
        });
    }
}
