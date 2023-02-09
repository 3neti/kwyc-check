<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Database\Seeders\UserSeeder;
use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @var User  */
    protected User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function users_table_has_a_seeder()
    {
        /*** assert ***/
        $this->assertDatabaseMissing('users', [
            'name' => 'Lester Hurtado',
            'email' => '3neti@lyflyn.net',
        ]);

        /*** act ***/
        $this->seed(UserSeeder::class);

        /*** assert ***/
        $this->assertDatabaseHas('users', [
            'name' => 'Lester Hurtado',
            'email' => '3neti@lyflyn.net',
        ]);
    }

    /** @test */
    public function user_has_zero_balance_in_his_wallet_initially()
    {
        $this->assertEquals(0, $this->user->wallet->balance);
    }

    /** @test */
    public function user_can_deposit_and_withdraw_from_his_wallet()
    {
        /*** arrange ***/
        $deposit_amount = 100;

        /*** act ***/
        $this->user->deposit($deposit_amount);

        /*** assert ***/
        $this->assertEquals($deposit_amount, $this->user->wallet->balance);

        /*** arrange ***/
        $withdrawal_amount = 25;
        $balance_amount = $deposit_amount - $withdrawal_amount;

        /*** act ***/
        $this->user->withdraw($withdrawal_amount);
        $this->assertEquals($balance_amount, $this->user->wallet->balance);
    }

    /** @test */
    public function user_can_transfer_amount_from_his_wallet()
    {
        /*** arrange ***/
        $initial_deposit = $this->faker->numberBetween(1000,100000);
        $this->user->deposit($initial_deposit);
        $user2 = User::factory()->createQuietly();
        $transfer_amount = $this->faker->numberBetween(10,100);
        $balance_amount = $initial_deposit - $transfer_amount;

        /*** act ***/
        $this->user->transfer($user2, $transfer_amount);

        /*** assert ***/
        $this->assertEquals($balance_amount, $this->user->wallet->balance);
        $this->assertEquals($transfer_amount, $user2->wallet->balance);
    }

    /** @test */
    public function user_can_confirm_wallet_transaction()
    {
        /*** arrange ***/
        $amount = $this->faker->numberBetween(1000,10000);;

        /*** act ***/
        $transaction = $this->user->deposit($amount, null, false);

        /*** assert ***/
        $this->assertFalse($transaction->confirmed);
        $this->assertEquals(0, $this->user->wallet->balance);

        /*** act ***/
        $this->user->confirm($transaction);

        /*** assert ***/
        $this->assertTrue($transaction->confirmed);
        $this->assertEquals($amount, $this->user->wallet->balance);
    }

    /** @test */
    public function user_can_transfer_wallet_amounts()
    {
        /*** arrange ***/
        $initial_deposit = $this->faker->numberBetween(1000,100000);
        $transfer_amount = $this->faker->numberBetween(10,100);
        $balance_amount = $initial_deposit - $transfer_amount;
        $attribs = ['name' => 'my-wallet'];

        /*** act ***/
        $wallet1 = $this->user->createWallet($attribs);
        $wallet2 = tap(User::factory()->createQuietly()->createWallet($attribs))
            ->deposit($initial_deposit);
        $wallet2->transfer($wallet1, $transfer_amount);

        /*** assert ***/
        $this->assertEquals($transfer_amount, $wallet1->balance);
        $this->assertEquals($balance_amount, $wallet2->balance);
    }

    /** @test */
    public function user_has_wallet_float()
    {
        /*** arrange ***/
        $initial_amount = $this->faker->numberBetween(1000,100000);

        /*** act ***/
        $this->user->deposit($initial_amount);

        /*** assert ***/
        $this->assertEquals($initial_amount/100, $this->user->balanceFloat);
    }
}
