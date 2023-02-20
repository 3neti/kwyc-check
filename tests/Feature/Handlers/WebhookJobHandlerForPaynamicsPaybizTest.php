<?php

namespace Tests\Feature\Handlers;

use App\Handlers\WebhookJobHandlerForPaynamicsPaybiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\WebhookClient\Models\WebhookCall;
use Illuminate\Support\Facades\Queue;
use App\Models\User;
use Tests\TestCase;

class WebhookJobHandlerForPaynamicsPaybizTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /** @test */
    public function webhook_job_handler_for_paynamics_paybiz_accepts_webhookcall_and_topups_existing_user()
    {
        /*** arrange ***/
        $user = User::factory()->create();
        $amount = $this->faker->numberBetween(100,10000);
        $job = $this->instantiateWebhookJobHandlerForPaynamicsPaybiz($user->mobile, $amount);

        /*** assert ***/
        $this->assertEquals(0, $user->balanceFloat);

        /*** act ***/
        $job->handle();

        /*** assert ***/
        $this->assertEquals($amount, $user->balanceFloat);
    }

    /** @test */
    public function webhook_job_handler_for_paynamics_paybiz_accepts_webhookcall_and_topups_new_user()
    {
        /*** arrange ***/
        $mobile = $this->faker->mobileNumber;
        $amount = $this->faker->numberBetween(100,10000);
        $job = $this->instantiateWebhookJobHandlerForPaynamicsPaybiz($mobile, $amount);

        /*** assert ***/
        $this->assertNull(User::fromMobile($mobile));

        /*** act ***/
        $job->handle();

        /*** assert ***/
        $this->assertEquals($amount, User::fromMobile($mobile)->balanceFloat);
    }

    /** @test */
    public function webhook_job_handler_for_paynamics_paybiz_end_point_existing_user()
    {
        /*** assert ***/
        Queue::fake();
        $user = User::factory()->create();
        $amount = $this->faker->numberBetween(100,10000);
        $payload = $this->createPayload($user->mobile, $amount);

        /*** act ***/
        $response = $this->postJson('webhook-paynamics-paybiz', $payload, []);

        /*** assert ***/
        $response->assertSuccessful();
        Queue::assertPushed(WebhookJobHandlerForPaynamicsPaybiz::class, function ($job) use ($payload) {
            return $job->webhookCall->payload == $payload;
        });
    }

    /** @test */
    public function webhook_job_handler_for_paynamics_paybiz_end_point_new_user()
    {
        /*** assert ***/
        Queue::fake();
        $mobile = $this->faker->mobileNumber;
        $amount = $this->faker->numberBetween(100,10000);
        $payload = $this->createPayload($mobile, $amount);

        /*** act ***/
        $response = $this->postJson('webhook-paynamics-paybiz', $payload, []);

        /*** assert ***/
        $response->assertSuccessful();
        Queue::assertPushed(WebhookJobHandlerForPaynamicsPaybiz::class, function ($job) use ($payload) {
            return $job->webhookCall->payload == $payload;
        });
    }

    protected function instantiateWebhookJobHandlerForPaynamicsPaybiz($mobile, $amount): WebhookJobHandlerForPaynamicsPaybiz
    {
        return new WebhookJobHandlerForPaynamicsPaybiz(
            WebhookCall::create([
                'name' => 'paynamics-paybiz',
                'url' => 'webhook-paynamics-paybiz',
                'payload' => $this->createPayload($mobile, $amount)
            ])
        );
    }

    private function createPayload($mobile, $amount): array
    {
        return [
            "pchannel" => "gc",
            "signature" => "2d811a752dfdd1603d2119f9a62e173beedf7e1d02daa295c8f14012b752e32c97dae5428eb70b63b55fd68a2e790816b3c3186d1d860b8df3f905a5dfa37a26",
            "timestamp" => "2023-02-19T01:19:29.000+08:00",
            "request_id" => "FTFP87P44ECM760VTZ",
            "merchant_id" => "00000027011198B17BFB",
            "response_id" => "53074965564452864",
            "customer_info" => [
                "zip" => "NA",
                "city" => "NA",
                "name" => "Francesca Gabrielle  Hurtado",
                "email" => "fgphurtado@me.com",
                "amount" => "{$amount}",
                "mobile" => "{$mobile}",
                "address" => "8 West Maya Drive, Philam Homes",
                "province" => "NA"
            ],
            "pay_reference" => "039664831",
            "response_code" => "GR001",
            "response_advise" => "Transaction is approved",
            "response_message" => "Transaction Successful",
            "processor_response_id" => "039664831"
        ];
    }
}
