<?php

namespace Tests\Feature\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Actions\TopupUser;
use App\Models\User;
use Tests\TestCase;

class TopupUserTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    /** @test */
    public function topup_user_action_accepts_user_and_float_amount()
    {
        /*** arrange ***/
        $user = User::factory()->create();
        $amount = $this->faker->randomFloat(2,100, 1000);

        /*** assert ***/
        $this->assertEquals(0, $user->balanceFloat);

        /*** act ***/
        $wallet = TopupUser::run($user, $amount);

        /*** assert ***/
        $this->assertTrue($user->is($wallet->holder));
        $this->assertEquals($amount, $user->balanceFloat);
    }

    /** @test */
    public function topup_user_action_end_point()
    {
        /*** arrange ***/
        $mobile = '09171234567'; //TODO: provider a more robust PH mobile faker
        $user = User::factory()->create(['mobile' => $mobile]);
        $amount = $this->faker->randomFloat(2,100, 1000);
        $token = User::factory()->create()->createToken('mobile')->plainTextToken;

        /*** act ***/
        $response = $this->postJson("/api/topup-user", [
            'mobile' => $mobile,
            'amount' => $amount
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        /*** assert ***/
        $response->assertSuccessful();
        $this->assertEquals($amount, $user->balanceFloat);
    }
}
