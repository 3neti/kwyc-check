<?php

namespace Tests\Feature\Notifications;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendOTPNotification;
use App\Models\Contact;
use App\Models\User;
use Tests\TestCase;

class SendOTPNotificationTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function send_otp_notification_to_user()
    {
        /*** arrange ***/
        Notification::fake();
        $mobile = '09171234567'; //TODO: provider a more robust PH mobile faker
        $user = User::factory()->create(['mobile' => $mobile]);
        $pin = $this->faker->text(5);

        /*** act ***/
        $user->notify(new SendOTPNotification($pin));

        /*** assert ***/
        Notification::assertSentTo($user, function(SendOTPNotification $notification) use ($user, $pin) {
            $mobile = null; $mode = null; $content = null;
            extract($notification->toArray($user));

            return phone($mobile, 'PH')->formatE164() == $user->mobile && $mode == 'sms' && $content == trans('domain.verify', ['pin' => $pin]);
        });
    }

    /** @test */
    public function send_otp_notification_to_contact()
    {
        /*** arrange ***/
        Notification::fake();
        $mobile = '09171234567'; //TODO: provider a more robust PH mobile faker
        $contact = Contact::factory()->create(['mobile' => $mobile]);
        $pin = $this->faker->text(5);

        /*** act ***/
        $contact->notify(new SendOTPNotification($pin));

        /*** assert ***/
        Notification::assertSentTo($contact, function(SendOTPNotification $notification) use ($contact, $pin) {
            $mobile = null; $mode = null; $message = null;
            extract($notification->toArray($contact));

            return phone($mobile, 'PH')->formatE164() == $contact->mobile && $mode == 'sms' && $message = trans('domain.verify', ['pin' => $pin]);
        });
    }
}
