<?php

namespace Tests\Feature\Notifications;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendOTPNotification;
use App\Models\User;
use Tests\TestCase;

class SendOTPNotificationTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function send_otp_notification()
    {
        /*** arrange ***/
        Notification::fake();
        $user = User::factory()->create();
        $pin = $this->faker->text(5);

        /*** act ***/
        $user->notify(new SendOTPNotification($pin));

        /*** assert ***/
        Notification::assertSentTo($user, function(SendOTPNotification $notification) use ($user, $pin) {
            $mobile = null; $mode = null; $message = null;
            extract($notification->toArray($user));

            return $mobile == $user->mobile && $mode == 'sms' && $message = trans('domain.verify', ['pin' => $pin]);
        });
    }
}
