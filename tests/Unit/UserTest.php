<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\UserSeeder;
use App\Models\OrganizationUser;
use App\Models\Organization;
use Illuminate\Support\Arr;
use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

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
        /*** arrange ***/
        $attribs = Arr::only(config('domain.seed.user.system'), ['name', 'email', 'mobile']);
//        foreach ($attribs as $key => $value) {
//            $attribs[$key] = decrypt($value);
//        }

        /*** assert ***/
        $this->assertDatabaseMissing('users', $attribs);

        /*** act ***/
        $this->seed(UserSeeder::class);

        /*** assert ***/
        $this->assertDatabaseHas('users', $attribs);
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
        $user2 = User::factory()->create();
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
        $wallet2 = tap(User::factory()->create()->createWallet($attribs))
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

    /** @test */
    public function user_accepts_mobile()
    {
        /*** arrange ***/
        $user = User::factory()->create(['mobile' => null]);
        $mobile = $this->faker->mobileNumber;

        /*** assert ***/
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'mobile' => null
        ]);

        /*** act ***/
        $user->mobile = $mobile;
        $user->save();

        /*** assert ***/
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'mobile' => $user->mobile
        ]);
    }

    /** @test */
    public function user_can_be_verified_via_otp()
    {
        /*** arrange ***/
        $user = User::factory()->create(['mobile_verified_at' => null]);

        /*** assert ***/
        $this->assertFalse($user->verified());

        /*** arrange ***/
        $user->challenge();
        $otp = $user->getTOTP();
        $pin = $otp->now();

        /*** act ***/
        $user->verify($pin);

        /*** assert ***/
        $this->assertTrue($user->verified());
    }

    /** @test */
    public function user_can_have_many_organizations_with_default_active()
    {
        /*** assert ***/
        $user = User::factory()->create();
        [$organization1, $organization2, $organization3] = Organization::factory(3)->create();

        /*** arrange ***/
        $user->organizations()->attach($organization1, ['active' => false]);
        $user->organizations()->attach($organization2);

        /*** act ***/
        $this->assertDatabaseHas(OrganizationUser::class, [
            'user_id' => $user->id,
            'organization_id' => $organization1->id,
            'active' => false
        ]);
        $this->assertDatabaseHas(OrganizationUser::class, [
            'user_id' => $user->id,
            'organization_id' => $organization2->id,
            'active' => true
        ]);
        $this->assertDatabaseMissing(OrganizationUser::class, [
            'user_id' => $user->id,
            'organization_id' => $organization3->id,
        ]);
    }

    /** @test */
    public function user_must_have_unique_organizations()
    {
        /*** assert ***/
        $this->expectExceptionCode(23000);

        /*** arrange ***/
        $user = User::factory()->create();
        [$organization1, $organization2] = Organization::factory(2)->create();
        $user->organizations()->attach($organization1);
        $user->organizations()->attach($organization2);

        /*** act ***/
        $user->organizations()->attach($organization1);
    }

    /** @test */
    public function user_from_mobile()
    {
        /*** act ***/
        /*** arrange ***/
        $user = User::factory()->create();
        $mobile = $user->mobile;

        /*** assert ***/
        $this->assertTrue($user->is(User::fromMobile($mobile)));
    }
}
