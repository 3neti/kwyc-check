<?php

namespace Tests\Feature\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Actions\GenerateHypervergeURLLink;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Request;
use Tests\TestCase;

class GenerateHypervergeURLLinkTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    /** @test */
    public function generate_hyperverge_url_link_accepts_transaction_id_and_array_inputs()
    {
        /*** arrange ***/
        $transactionId = $this->faker->uuid();
        $url = $this->faker->url();
        $json = $this->getJsonResponse($url);
//        $inputs = ['x' => 'y', 'a' => 'b'];
        Http::fake([
            config('domain.hyperverge.api.url.kyc') => Http::response($json, 200)
        ]);

        /*** act ***/
        $result = GenerateHypervergeURLLink::run($transactionId);

        /*** assert ***/
        Http::assertSent(function (Request $request) {
            return
                $request->hasHeader('appId', config('domain.hyperverge.api.id'))
                && $request->hasHeader('appKey', config('domain.hyperverge.api.key'))
                && $request->url() == config('domain.hyperverge.api.url.kyc')
                && $request['workflowId'] == config('domain.hyperverge.url.workflow')
                && $request['redirectUrl'] == route('hyperverge-result')
//                && $request['inputs'] == array_merge(['app' => config('app.name')], $inputs)
                && $request['languages'] ==  ['en' => 'English']
                && $request['defaultLanguage'] ==  'en'
                && $request['expiry'] == config('domain.hyperverge.api.expiry');
        });
        $this->assertEquals($url, $result);
    }

    protected function getJsonResponse(string $url): string
    {
        return <<<EOT
{
    "status": "success",
    "statusCode": 200,
    "metadata": {
        "requestId": "1677292617660-8e117723-1dac-4509-9880-698e2514de17"
    },
    "result": {
        "startKycUrl": "$url"
    }
}
EOT;
    }
}
