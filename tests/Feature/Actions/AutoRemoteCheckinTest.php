<?php

namespace Tests\Feature\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Notifications\NewCheckinNotification;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use App\Actions\Checkin\AutoRemoteCheckin;
use App\Actions\GenerateHypervergeURLLink;
use Mockery\MockInterface;
use App\Models\Contact;
use App\Models\User;
use Tests\TestCase;

class AutoRemoteCheckinTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    /** @test */
    public function new_checkin_accepts_user_and_creates_checkin()
    {
        /*** arrange ***/
        Notification::fake();
        $url = $this->faker->url();
        $this->mock(GenerateHypervergeURLLink::class, function (MockInterface $mock) use ($url) {
            $mock->shouldReceive('handle')->once()->andReturn($url);
        });
        $user = User::factory()->create();

        /*** act ***/
        $checkin = AutoRemoteCheckin::run($user);

        /*** assert ***/
        $this->assertEquals($url, $checkin->url);
        $this->assertTrue($user->is($checkin->agent));
        $this->assertNull($checkin->person);
        Notification::assertSentTo($user, NewCheckinNotification::class, function ($notification) use ($user, $url) {
            return $notification->getContent($user) == trans('domain.new-checkin', ['url' => $url]);
        });
    }

    /** @test */
    public function new_checkin_accepts_user_mobile_creates_checkin_and_associates_person()
    {
        /*** arrange ***/
        GenerateHypervergeURLLink::shouldRun();
        $contact_mobile = $this->faker->mobileNumber();

        /*** act ***/
        $checkin = AutoRemoteCheckin::run(User::factory()->create(), $contact_mobile);

        /*** assert ***/
        $this->assertTrue($checkin->person->is(Contact::fromMobile($contact_mobile)));
    }
}
