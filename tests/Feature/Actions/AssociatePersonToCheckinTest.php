<?php

namespace Tests\Feature\Actions;

use App\Actions\Checkin\AssociatePersonToCheckin;
use App\Models\Checkin;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AssociatePersonToCheckinTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    /** @test */
    public function associate_person_to_checkin_accepts_checkin_and_mobile()
    {
        /*** arrange ***/
        $checkin = Checkin::factory()->create(['person_id' => null, 'person_type' => null]);
        $mobile = $this->faker->mobileNumber();

        /*** assert ***/
        $this->assertNull($checkin->person);

        /*** act ***/
        AssociatePersonToCheckin::run($checkin, $mobile);

        /*** assert ***/
        $this->assertNotNull($contact = Contact::fromMobile($mobile));
        $this->assertNotNull($checkin->person);
        $this->assertTrue($contact->is($checkin->person));
    }
}
