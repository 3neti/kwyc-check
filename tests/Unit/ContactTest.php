<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Database\QueryException;
use App\Models\Contact;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function contact_requires_mobile()
    {
        /*** assert ***/
        $this->expectException(QueryException::class);

        /*** arrange ***/
        $contact = Contact::make();
        $contact->handle = $this->faker->e164PhoneNumber();
        $contact->data = $this->faker->rgbColorAsArray();

        /*** act ***/
        $contact->save();
    }

    /** @test */
    public function contact_accepts_mobile_handle_and_data()
    {
        /*** arrange ***/
        $mobile = $this->faker->e164PhoneNumber();
        $handle = $this->faker->name();
        $data = $this->faker->rgbColorAsArray();

        /*** act ***/
        $contact = Contact::create(compact('mobile', 'handle', 'data'));

        /*** assert ***/
        $this->assertDatabaseHas('contacts', [
            'mobile' => $mobile,
            'handle' => $handle,
        ]);
        $this->assertSame($data, $contact->data->toArray());
    }

    /** @test */
    public function contact_default_handle_is_mobile()
    {
        /*** arrange ***/
        $mobile = $this->faker->e164PhoneNumber();

        /*** act ***/
        $contact = Contact::factory()->create(compact('mobile'));

        /*** assert ***/
        $this->assertSame($mobile, $contact->handle);
    }

    /** @test */
    public function contact_can_be_verified_via_otp()
    {
        /*** arrange ***/
        $contact = Contact::factory()->create(['mobile_verified_at' => null]);

        /*** assert ***/
        $this->assertFalse($contact->verified());

        /*** arrange ***/
        $contact->challenge();
        $otp = $contact->getTOTP();
        $pin = $otp->now();

        /*** act ***/
        $contact->verify($pin);

        /*** assert ***/
        $this->assertTrue($contact->verified());
    }
}
