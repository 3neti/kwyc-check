<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use MOIREI\Vouchers\Facades\Vouchers;
use MOIREI\Vouchers\VoucherScheme;
use App\Models\Campaign;
use App\Models\User;
use Tests\TestCase;

class VoucherTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function voucher_with_attributes_can_be_generated_for_a_campaign()
    {
        /*** arrange ***/
        $campaign = Campaign::factory()->create();
        $attributes = [
            'limit_scheme' => VoucherScheme::REDEEMER,
            'quantity' => 2
        ];

        /*** act ***/
        $voucher = Vouchers::create($campaign, $attributes);

        /*** assert ***/
        $this->assertTrue($voucher->isItem($campaign));
    }

    /** @test */
    public function voucher_with_attributes_can_be_redeemed_by_a_user()
    {
        /*** arrange ***/
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create();
        $data = [
            'brothers' => [
                'Dene' => ['birthdate' => 'April 1, 1971'],
                'Glen' => ['birthdate' => 'October 29, 1972'],
            ],
            'sisters' => [
                'Jo Anna' => ['birthdate' => 'March 5, 1974'],
                'Rowena' => ['birthdate' => 'April 18, 1975'],
            ],
        ];

        $attributes = [
            'limit_scheme' => VoucherScheme::REDEEMER,
            'quantity' => 2,
            'data' => $data
        ];

        $voucher = $campaign->createVoucher($attributes);
        $item = $voucher->items->shift()->item;

        /*** act ***/
        $voucher = $user->redeem($voucher, $item);

        /*** assert ***/
        $this->assertTrue($voucher->isItem($campaign));
        $this->assertTrue($campaign->is($item));
        $this->assertEquals($data, $voucher->data->all());
    }
}
