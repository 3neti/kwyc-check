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
        $contact->handle = $this->faker->name();
        $contact->data = $this->faker->rgbColorAsArray();

        /*** act ***/
        $contact->save();
    }

    /** @test */
    public function contact_accepts_mobile_handle_and_data()
    {
        /*** arrange ***/
        $mobile = '09171234567'; //TODO: provider a more robust PH mobile faker
        $handle = $this->faker->name();
        $data = $this->faker->rgbColorAsArray();

        /*** act ***/
        $contact = Contact::create(compact('mobile', 'handle', 'data'));

        /*** assert ***/
        $this->assertDatabaseHas('contacts', [
            'mobile' => phone($mobile, 'PH')->formatE164(),
            'handle' => $handle,
        ]);
        $this->assertSame($data, $contact->data->toArray());
    }

    /** @test */
    public function contact_default_handle_is_mobile()
    {
        /*** arrange ***/
        $mobile = '09171234567'; //TODO: provider a more robust PH mobile faker
        /*** act ***/
        $contact = Contact::factory()->create(compact('mobile'));

        /*** assert ***/
        $this->assertSame(phone($mobile, 'PH')->formatE164(), $contact->handle);
    }

    /** @test */
    public function contact_can_be_verified_via_otp()
    {
        /*** arrange ***/
        $mobile = '09171234567'; //TODO: provider a more robust PH mobile faker
        $contact = Contact::factory()->create([
            'mobile' => $mobile,
            'mobile_verified_at' => null
        ]);

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

    /** @test */
    public function contact_from_mobile()
    {
        /*** arrange ***/
        $mobile = '09171234567';

        /*** act ***/
        $contact = Contact::factory()->create(compact('mobile'));

        /*** assert ***/
        $this->assertTrue($contact->is(Contact::fromMobile($mobile)));
    }
}
