<?php

namespace Tests\Feature\Notifications;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Notifications\SendRegisterUserNotification;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use App\Models\Campaign;
use App\Models\User;
use Tests\TestCase;

class SendRegisterUserNotificationTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function send_campaign_notification_accepts_campaign()
    {
        /*** arrange ***/
        Notification::fake();
        $user = User::factory()->create(['mobile' => $this->faker->e164PhoneNumber()]);
        $campaign = Campaign::factory()->create();

        /*** act ***/
        $user->notify(new SendRegisterUserNotification($campaign));

        /*** assert ***/
        Notification::assertSentTo($user, function(SendRegisterUserNotification $notification) use ($user) {
            $register_user_message = $notification->getContent($user);

            return $register_user_message == trans('domain.org-campaign', [
                    'org' => $notification->getCampaign()->repository->organization->name,
                    'url' => route('register-user', [
                        'org_id' => $notification->getCampaign()->repository->organization->id
                    ])
                ]);
        });
    }
}
