<?php

namespace Tests\Feature\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Actions\Checkin\HydrateCheckinPerson;
use Illuminate\Foundation\Testing\WithFaker;
use App\Actions\ProcessHypervergeResult;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Arr;
use App\Models\Checkin;
use Tests\TestCase;

class ProcessHypervergeResultTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    /** @test */
    public function process_hyperverge_result_accepts_checkin_fetches_and_appends_result_to_data()
    {
        /*** arrange ***/
        Queue::fake();
        $checkin = Checkin::factory()->create();
        $json = $this->getJsonResponse($checkin->uuid);
        $array = json_decode($json, true);
        Http::fake([
            config('domain.hyperverge.api.url.result') => Http::response($json, 200)
        ]);

        /*** act ***/
        $result = ProcessHypervergeResult::run($checkin);

        /*** assert ***/
        Http::assertSent(function (Request $request) use ($checkin) {
            return
                $request->hasHeader('appId', config('domain.hyperverge.api.id')) &&
                $request->hasHeader('appKey', config('domain.hyperverge.api.key')) &&
                $request->url() == config('domain.hyperverge.api.url.result') &&
                $request['transactionId'] ==  $checkin->uuid;
        });
        $this->assertTrue($result);
        foreach (config('domain.hyperverge.mapping') as $key=>$value) {
            $this->assertEquals(Arr::get($array, $value), $checkin->getAttribute($key));
        }
        HydrateCheckinPerson::assertPushed();
    }

    protected function getJsonResponse($transactionId): string
    {
        return <<<EOT
{
    "status": "success",
    "statusCode": 200,
    "metadata": {
        "requestId": "1677241068652-29eebd5e-61bd-4b8b-bd1b-b748474737a0",
        "transactionId": "$transactionId"
    },
    "result": {
        "workflowDetails": {
            "workflowId": "default",
            "version": 1
        },
        "applicationStatus": "auto_approved",
        "results": [
            {
                "module": "undefined front",
                "countrySelected": "phl",
                "documentSelected": "dl",
                "attempts": "3",
                "expectedDocumentSide": "front",
                "moduleId": "module_id",
                "croppedImageUrl": "https://prod-audit-portal-sgp.s3.ap-southeast-1.amazonaws.com/gkyc-ap-southeast-1/readId/2023-02-24/12dqkm/1677231240564-10588323-852f-4bce-b35c-acb6e9cb080a/cropped.jpeg?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=ASIAZRKK5ZMRZ3IN3VWS%2F20230224%2Fap-southeast-1%2Fs3%2Faws4_request&X-Amz-Date=20230224T121749Z&X-Amz-Expires=900&X-Amz-Security-Token=IQoJb3JpZ2luX2VjEGwaCmFwLXNvdXRoLTEiRjBEAiAU9QkLu6LeGVALccMsO5LeSqgnGH1UWJpgvQSty8uyPwIgAklKiz0%2F8Sij2YvWNFTAMMorMl7ssS87AYZLwHyJeAcqvwUIFRABGgw2NTU2NzY1MjUzNDciDM3VbaaaWD8Xi%2FKTXiqcBePfcoo%2FmE33G5Y9Tyk1mqcJfEa1Ufgt9bi%2FKPGKqebTgdyMrG6bGZGn%2BcB2xyZkztmUJ%2ByzZeaBG9stEZKwuEZFyQxXw6kF2iSIkWZq3%2F7ZThb9FDhMP0DjYMEdimfQO45BSbsJAZ%2BTEV1lttF1qOOUrXSOs5W%2BCJhW06RgeiPh6xzCoDv236MAswmsEE%2BWzc6ca28Eeu9d71Mas0P%2FJti0pktsd%2BZbIG0PRkfrOGyaS3EXRbZV4Ju5JOAhyq%2FaMoPPPSi35XIOiRGCOyo7y2TX1y6Qra%2BAmVFlFGqNrXBbU6w0Iht%2F%2B5PfYJsrCxMCSCNZuqCwLuGU5J7EN1u5FRyYipNWo%2FujTLeeuX4r1xWyx2iIz1ZrKVMOT5%2B%2Fkmjl7Rgtzf1DluF4OMmwn942m%2BNS8niMahYODiA2%2FuC9b1DlIJ7bSk4XzavkkxtbhTNOXOs5q2dm%2Bl92ZFb9oNxJClV1bXh36ML8XKMbJH7CI62bnm4O5PPMVfKiHPrsGrbSmNylHAc2lgWuMaMAN1hsePBp%2Fv%2FFe8drrYs5Nskvn1RuDFEN76xW59kXKikz2%2FtFD5UkPZrBJ2F2qYCaD6FjjRpL4VNoObfMygtYBvqCdPH8JZSa3fAWlYp%2FngnGBAYbjdPV0Y6oYOqd5sHrf8z0qW9aFAYgkwWoEH%2B6ExkdW0IQ5QHr1w%2FB8D2gIcg2AuyFRBtkV5Dq0spy6R8nt%2FTKneq8RxONnEK6EuxkhbPqMz3yka2lN%2F3E2r3gkSWE33nobIRR0dZNVK8g1o2AdWKDR8KBr%2FOqANI0ITQV306bYZAvJwctyZcxxRfvcQuuPQi0KJs9cA3%2Bfw55zX5jX4cbz6u9c3FLK35BHR2Qh%2B%2B68BOCSbVXXZWfn9D5dbdFMNzM4p8GOrIBaFnGHF3wAAOAQ8K6ZRHvQ6%2FQysVnYZlQihROYF%2FWDWPOUrPbUGij%2FT1zb%2F1L42ifBOWTdcJYhSDxXX%2FKAtOiyl3%2B1zlYKVyjwe84g81wcBF8q7ancEX7XUtnVGqCRgdWlkwcq6wqX%2FY6GGLNtH3QfNOmpTmZ456Gf7Wa%2Ffu4enLNEGOgD2TzODD9J8AjS94o9xTvkP%2FCqZKgikDO3NvRxnULHfTeiF%2Fo7ukCFIgfr3twsA%3D%3D&X-Amz-Signature=09b0c69cb3d08c4ae156c13cbf022eb8613ec33a047a38be7580a91bf2abf807&X-Amz-SignedHeaders=host",
                "imageUrl": "https://prod-audit-portal-sgp.s3.ap-southeast-1.amazonaws.com/gkyc-ap-southeast-1/readId/2023-02-24/12dqkm/1677231240564-10588323-852f-4bce-b35c-acb6e9cb080a/image.jpeg?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=ASIAZRKK5ZMRZ3IN3VWS%2F20230224%2Fap-southeast-1%2Fs3%2Faws4_request&X-Amz-Date=20230224T121749Z&X-Amz-Expires=900&X-Amz-Security-Token=IQoJb3JpZ2luX2VjEGwaCmFwLXNvdXRoLTEiRjBEAiAU9QkLu6LeGVALccMsO5LeSqgnGH1UWJpgvQSty8uyPwIgAklKiz0%2F8Sij2YvWNFTAMMorMl7ssS87AYZLwHyJeAcqvwUIFRABGgw2NTU2NzY1MjUzNDciDM3VbaaaWD8Xi%2FKTXiqcBePfcoo%2FmE33G5Y9Tyk1mqcJfEa1Ufgt9bi%2FKPGKqebTgdyMrG6bGZGn%2BcB2xyZkztmUJ%2ByzZeaBG9stEZKwuEZFyQxXw6kF2iSIkWZq3%2F7ZThb9FDhMP0DjYMEdimfQO45BSbsJAZ%2BTEV1lttF1qOOUrXSOs5W%2BCJhW06RgeiPh6xzCoDv236MAswmsEE%2BWzc6ca28Eeu9d71Mas0P%2FJti0pktsd%2BZbIG0PRkfrOGyaS3EXRbZV4Ju5JOAhyq%2FaMoPPPSi35XIOiRGCOyo7y2TX1y6Qra%2BAmVFlFGqNrXBbU6w0Iht%2F%2B5PfYJsrCxMCSCNZuqCwLuGU5J7EN1u5FRyYipNWo%2FujTLeeuX4r1xWyx2iIz1ZrKVMOT5%2B%2Fkmjl7Rgtzf1DluF4OMmwn942m%2BNS8niMahYODiA2%2FuC9b1DlIJ7bSk4XzavkkxtbhTNOXOs5q2dm%2Bl92ZFb9oNxJClV1bXh36ML8XKMbJH7CI62bnm4O5PPMVfKiHPrsGrbSmNylHAc2lgWuMaMAN1hsePBp%2Fv%2FFe8drrYs5Nskvn1RuDFEN76xW59kXKikz2%2FtFD5UkPZrBJ2F2qYCaD6FjjRpL4VNoObfMygtYBvqCdPH8JZSa3fAWlYp%2FngnGBAYbjdPV0Y6oYOqd5sHrf8z0qW9aFAYgkwWoEH%2B6ExkdW0IQ5QHr1w%2FB8D2gIcg2AuyFRBtkV5Dq0spy6R8nt%2FTKneq8RxONnEK6EuxkhbPqMz3yka2lN%2F3E2r3gkSWE33nobIRR0dZNVK8g1o2AdWKDR8KBr%2FOqANI0ITQV306bYZAvJwctyZcxxRfvcQuuPQi0KJs9cA3%2Bfw55zX5jX4cbz6u9c3FLK35BHR2Qh%2B%2B68BOCSbVXXZWfn9D5dbdFMNzM4p8GOrIBaFnGHF3wAAOAQ8K6ZRHvQ6%2FQysVnYZlQihROYF%2FWDWPOUrPbUGij%2FT1zb%2F1L42ifBOWTdcJYhSDxXX%2FKAtOiyl3%2B1zlYKVyjwe84g81wcBF8q7ancEX7XUtnVGqCRgdWlkwcq6wqX%2FY6GGLNtH3QfNOmpTmZ456Gf7Wa%2Ffu4enLNEGOgD2TzODD9J8AjS94o9xTvkP%2FCqZKgikDO3NvRxnULHfTeiF%2Fo7ukCFIgfr3twsA%3D%3D&X-Amz-Signature=3f814508f4a6b6139bbaf2edfd0fa23346edcf675bccaf1160339bd6e4c64a1b&X-Amz-SignedHeaders=host",
                "apiResponse": {
                    "status": "success",
                    "statusCode": 200,
                    "metadata": {
                        "requestId": "1677231240564-10588323-852f-4bce-b35c-acb6e9cb080a",
                        "transactionId": "$transactionId"
                    },
                    "result": {
                        "details": [
                            {
                                "idType": "phl_dl",
                                "fieldsExtracted": {
                                    "firstName": {
                                        "value": ""
                                    },
                                    "middleName": {
                                        "value": ""
                                    },
                                    "lastName": {
                                        "value": ""
                                    },
                                    "fullName": {
                                        "value": "HURTADO, LESTER BIADORA"
                                    },
                                    "dateOfBirth": {
                                        "value": "21-04-1970"
                                    },
                                    "dateOfIssue": {
                                        "value": ""
                                    },
                                    "dateOfExpiry": {
                                        "value": "21-04-2027"
                                    },
                                    "countryCode": {
                                        "value": ""
                                    },
                                    "type": {
                                        "value": ""
                                    },
                                    "address": {
                                        "value": "8 WEST MAYA DRIVE PHILAM HOMES QUEZON CITY",
                                        "houseNumber": "",
                                        "province": "",
                                        "street": "",
                                        "district": "",
                                        "zipCode": "",
                                        "additionalInfo": ""
                                    },
                                    "gender": {
                                        "value": "M"
                                    },
                                    "idNumber": {
                                        "value": "N01-87-049586"
                                    },
                                    "placeOfBirth": {
                                        "value": ""
                                    },
                                    "placeOfIssue": {
                                        "value": ""
                                    },
                                    "yearOfBirth": {
                                        "value": "1970"
                                    },
                                    "age": {
                                        "value": ""
                                    },
                                    "fatherName": {
                                        "value": ""
                                    },
                                    "motherName": {
                                        "value": ""
                                    },
                                    "husbandName": {
                                        "value": ""
                                    },
                                    "spouseName": {
                                        "value": ""
                                    },
                                    "nationality": {
                                        "value": "PHL"
                                    },
                                    "mrzString": {
                                        "value": "",
                                        "idNumber": "",
                                        "fullName": "",
                                        "dateOfBirth": "",
                                        "dateOfExpiry": "",
                                        "gender": "",
                                        "nationality": ""
                                    },
                                    "homeTown": {
                                        "value": ""
                                    }
                                },
                                "croppedImageUrl": "https://sg-kyc-hyperverge-co.s3.ap-southeast-1.amazonaws.com/undefined/2023-02-24/6ad9ef/1677231240838-6bfae7f4-dc82-477c-9489-668ca0b6ab7de377648d-999d-4a49-aef2-4b907e089e0a-ycOqAUcpjB_phl_dl_1.jpg?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=ASIAXB3KY4F5HQFS6SM7%2F20230224%2Fap-southeast-1%2Fs3%2Faws4_request&X-Amz-Date=20230224T093401Z&X-Amz-Expires=900&X-Amz-Security-Token=IQoJb3JpZ2luX2VjEGYaDmFwLXNvdXRoZWFzdC0xIkcwRQIhAIzh9a0bfnjHmcp%2BrIvs53ClDj1%2BaGnVu73XhCKrk%2B5IAiBoP6EZqieXW%2Fj9DT%2BJ5eJi3Nq0bKOL9urSGUkkh5PXayrkBAj%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F8BEAAaDDQ4NTAxODM2MjIzNCIMv6vYdt8mRSgtCLGxKrgEAMXnEQYFKTtTNZcqUR6%2Bph5M2KBTSfX0wIczAoaVgGG8aefidh%2B71RHNb%2FemeqNl1XtxeFtTB%2BQAT3BV2XEnVow4J6TF16VPjGYCu9jeZsMinddYefJwaMdVVqdvEOHQgDkMCG5XB5PVyUbz3FHlMNNaW5Hj1g0%2F%2FNLOO1jzLP7TRAtaYpJUEgCbAD5tqA3BVtqkekznzd10K9O7IOt94DbXiEJYFhdLP9F%2FOiO8%2FNR5LPPzrOPEnIFQUbw53gVBd0BvaT7DdGng3Ak6%2BxlhfwDO%2BmEe5yeUms9oeR55I2cCAXH2sZXPuM9kiQ%2FiDAQerqNyXCxfIMXGsZUV2pOeH3b15AZNSjzVOAFviF1lj4LWtF77zrQches%2B9HWd9Ro1%2FCRT1Mk0W3c9vcYZwHNcXV4yq6cKniwprXJmETpXDaIFpNZ2XYm1Z2Rz%2FVzlNSoBLwzVioFTxbMpOGmm4VUhE%2Ffdk9GhAoZzZJJY1GIgGWiMm43VFxeB%2BYskWSEbcPAgR7eh9PJSiK6xlyPSo%2FNt1pBcmZTk%2FwDfseRahJoxGEw1l1vI6GXT8mR2V1dv9FPPiwIA%2BcVdR%2BgqaT7zfOn2S%2B%2FCiZ03VGItHMbUm2ZmJCZbS9EuIS%2B1qRyoBg02N3RdUOuzMasOfJQLK7HDUoWNBHfwGKwjASqdWKMCpAekZHYh0HYyev6c5apAQIIK01V%2F2%2BzPLZ7J4TLMkfGWSu20uwyTvJ30vyZ%2BUuIu3nT6O3f6Bcy5zYxpjDCyqeGfBjqpAYkxI%2F0Ad8YB2JyxmmpL3dIZD%2FQBS1wa2QhCt074fN8TFPUE%2B%2FwIQcMqqCgQpbRfgzPkZN1ahjachvwMqY2cPzaY%2B%2BNYymxafbQV5Z4x0oT1Pqme1DyPMDhHKWZdqpkaCvMtCVRHre7TyePUM%2FrJnG3wsyb3G7Zf0MQIwgqGAOEjSCqAzmZkLjkRKSve3s6QZSxzX6WgCQFaLbvpTeGEcR37srq70J%2FQqME%3D&X-Amz-Signature=6fadb1b6ab1836fee614a890500a4dbbe9d3a56dda96605ca42f4792b8834d5b&X-Amz-SignedHeaders=host",
                                "qualityChecks": {
                                    "blur": {
                                        "value": "no"
                                    },
                                    "glare": {
                                        "value": "no"
                                    },
                                    "blackAndWhite": {
                                        "value": "no"
                                    },
                                    "capturedFromScreen": {
                                        "value": "no"
                                    },
                                    "partialId": {
                                        "value": "no"
                                    }
                                }
                            }
                        ],
                        "summary": {
                            "action": "pass",
                            "details": []
                        }
                    }
                },
                "previousAttempts": [
                    {
                        "module": "undefined front",
                        "countrySelected": "phl",
                        "documentSelected": "dl",
                        "attempts": "1",
                        "expectedDocumentSide": "front",
                        "moduleId": "module_id",
                        "croppedImageUrl": "https://prod-audit-portal-sgp.s3.ap-southeast-1.amazonaws.com/gkyc-ap-southeast-1/readId/2023-02-24/12dqkm/1677231199552-db6e8191-fadd-45da-9f3d-8509dbb20b80/cropped.jpeg?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=ASIAZRKK5ZMRZ3IN3VWS%2F20230224%2Fap-southeast-1%2Fs3%2Faws4_request&X-Amz-Date=20230224T121749Z&X-Amz-Expires=900&X-Amz-Security-Token=IQoJb3JpZ2luX2VjEGwaCmFwLXNvdXRoLTEiRjBEAiAU9QkLu6LeGVALccMsO5LeSqgnGH1UWJpgvQSty8uyPwIgAklKiz0%2F8Sij2YvWNFTAMMorMl7ssS87AYZLwHyJeAcqvwUIFRABGgw2NTU2NzY1MjUzNDciDM3VbaaaWD8Xi%2FKTXiqcBePfcoo%2FmE33G5Y9Tyk1mqcJfEa1Ufgt9bi%2FKPGKqebTgdyMrG6bGZGn%2BcB2xyZkztmUJ%2ByzZeaBG9stEZKwuEZFyQxXw6kF2iSIkWZq3%2F7ZThb9FDhMP0DjYMEdimfQO45BSbsJAZ%2BTEV1lttF1qOOUrXSOs5W%2BCJhW06RgeiPh6xzCoDv236MAswmsEE%2BWzc6ca28Eeu9d71Mas0P%2FJti0pktsd%2BZbIG0PRkfrOGyaS3EXRbZV4Ju5JOAhyq%2FaMoPPPSi35XIOiRGCOyo7y2TX1y6Qra%2BAmVFlFGqNrXBbU6w0Iht%2F%2B5PfYJsrCxMCSCNZuqCwLuGU5J7EN1u5FRyYipNWo%2FujTLeeuX4r1xWyx2iIz1ZrKVMOT5%2B%2Fkmjl7Rgtzf1DluF4OMmwn942m%2BNS8niMahYODiA2%2FuC9b1DlIJ7bSk4XzavkkxtbhTNOXOs5q2dm%2Bl92ZFb9oNxJClV1bXh36ML8XKMbJH7CI62bnm4O5PPMVfKiHPrsGrbSmNylHAc2lgWuMaMAN1hsePBp%2Fv%2FFe8drrYs5Nskvn1RuDFEN76xW59kXKikz2%2FtFD5UkPZrBJ2F2qYCaD6FjjRpL4VNoObfMygtYBvqCdPH8JZSa3fAWlYp%2FngnGBAYbjdPV0Y6oYOqd5sHrf8z0qW9aFAYgkwWoEH%2B6ExkdW0IQ5QHr1w%2FB8D2gIcg2AuyFRBtkV5Dq0spy6R8nt%2FTKneq8RxONnEK6EuxkhbPqMz3yka2lN%2F3E2r3gkSWE33nobIRR0dZNVK8g1o2AdWKDR8KBr%2FOqANI0ITQV306bYZAvJwctyZcxxRfvcQuuPQi0KJs9cA3%2Bfw55zX5jX4cbz6u9c3FLK35BHR2Qh%2B%2B68BOCSbVXXZWfn9D5dbdFMNzM4p8GOrIBaFnGHF3wAAOAQ8K6ZRHvQ6%2FQysVnYZlQihROYF%2FWDWPOUrPbUGij%2FT1zb%2F1L42ifBOWTdcJYhSDxXX%2FKAtOiyl3%2B1zlYKVyjwe84g81wcBF8q7ancEX7XUtnVGqCRgdWlkwcq6wqX%2FY6GGLNtH3QfNOmpTmZ456Gf7Wa%2Ffu4enLNEGOgD2TzODD9J8AjS94o9xTvkP%2FCqZKgikDO3NvRxnULHfTeiF%2Fo7ukCFIgfr3twsA%3D%3D&X-Amz-Signature=85562e097db85cec8f139c3ecce30fa8ee95d782eca24002608d44ea242202ba&X-Amz-SignedHeaders=host",
                        "imageUrl": "https://prod-audit-portal-sgp.s3.ap-southeast-1.amazonaws.com/gkyc-ap-southeast-1/readId/2023-02-24/12dqkm/1677231199552-db6e8191-fadd-45da-9f3d-8509dbb20b80/image.jpeg?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=ASIAZRKK5ZMRZ3IN3VWS%2F20230224%2Fap-southeast-1%2Fs3%2Faws4_request&X-Amz-Date=20230224T121749Z&X-Amz-Expires=900&X-Amz-Security-Token=IQoJb3JpZ2luX2VjEGwaCmFwLXNvdXRoLTEiRjBEAiAU9QkLu6LeGVALccMsO5LeSqgnGH1UWJpgvQSty8uyPwIgAklKiz0%2F8Sij2YvWNFTAMMorMl7ssS87AYZLwHyJeAcqvwUIFRABGgw2NTU2NzY1MjUzNDciDM3VbaaaWD8Xi%2FKTXiqcBePfcoo%2FmE33G5Y9Tyk1mqcJfEa1Ufgt9bi%2FKPGKqebTgdyMrG6bGZGn%2BcB2xyZkztmUJ%2ByzZeaBG9stEZKwuEZFyQxXw6kF2iSIkWZq3%2F7ZThb9FDhMP0DjYMEdimfQO45BSbsJAZ%2BTEV1lttF1qOOUrXSOs5W%2BCJhW06RgeiPh6xzCoDv236MAswmsEE%2BWzc6ca28Eeu9d71Mas0P%2FJti0pktsd%2BZbIG0PRkfrOGyaS3EXRbZV4Ju5JOAhyq%2FaMoPPPSi35XIOiRGCOyo7y2TX1y6Qra%2BAmVFlFGqNrXBbU6w0Iht%2F%2B5PfYJsrCxMCSCNZuqCwLuGU5J7EN1u5FRyYipNWo%2FujTLeeuX4r1xWyx2iIz1ZrKVMOT5%2B%2Fkmjl7Rgtzf1DluF4OMmwn942m%2BNS8niMahYODiA2%2FuC9b1DlIJ7bSk4XzavkkxtbhTNOXOs5q2dm%2Bl92ZFb9oNxJClV1bXh36ML8XKMbJH7CI62bnm4O5PPMVfKiHPrsGrbSmNylHAc2lgWuMaMAN1hsePBp%2Fv%2FFe8drrYs5Nskvn1RuDFEN76xW59kXKikz2%2FtFD5UkPZrBJ2F2qYCaD6FjjRpL4VNoObfMygtYBvqCdPH8JZSa3fAWlYp%2FngnGBAYbjdPV0Y6oYOqd5sHrf8z0qW9aFAYgkwWoEH%2B6ExkdW0IQ5QHr1w%2FB8D2gIcg2AuyFRBtkV5Dq0spy6R8nt%2FTKneq8RxONnEK6EuxkhbPqMz3yka2lN%2F3E2r3gkSWE33nobIRR0dZNVK8g1o2AdWKDR8KBr%2FOqANI0ITQV306bYZAvJwctyZcxxRfvcQuuPQi0KJs9cA3%2Bfw55zX5jX4cbz6u9c3FLK35BHR2Qh%2B%2B68BOCSbVXXZWfn9D5dbdFMNzM4p8GOrIBaFnGHF3wAAOAQ8K6ZRHvQ6%2FQysVnYZlQihROYF%2FWDWPOUrPbUGij%2FT1zb%2F1L42ifBOWTdcJYhSDxXX%2FKAtOiyl3%2B1zlYKVyjwe84g81wcBF8q7ancEX7XUtnVGqCRgdWlkwcq6wqX%2FY6GGLNtH3QfNOmpTmZ456Gf7Wa%2Ffu4enLNEGOgD2TzODD9J8AjS94o9xTvkP%2FCqZKgikDO3NvRxnULHfTeiF%2Fo7ukCFIgfr3twsA%3D%3D&X-Amz-Signature=fc5f9d497c7569a0ff52a6c42f5a7182f4a4a9b59932f0f80af6e0dfd22cc933&X-Amz-SignedHeaders=host",
                        "apiResponse": {
                            "status": "success",
                            "statusCode": 200,
                            "metadata": {
                                "requestId": "1677231199552-db6e8191-fadd-45da-9f3d-8509dbb20b80",
                                "transactionId": "$transactionId"
                            },
                            "result": {
                                "details": [
                                    {
                                        "idType": "phl_dl",
                                        "fieldsExtracted": {
                                            "firstName": {
                                                "value": ""
                                            },
                                            "middleName": {
                                                "value": ""
                                            },
                                            "lastName": {
                                                "value": ""
                                            },
                                            "fullName": {
                                                "value": "HURTADO, LESTER BIADORA"
                                            },
                                            "dateOfBirth": {
                                                "value": "21-04-1970"
                                            },
                                            "dateOfIssue": {
                                                "value": ""
                                            },
                                            "dateOfExpiry": {
                                                "value": "21-04-2027"
                                            },
                                            "countryCode": {
                                                "value": ""
                                            },
                                            "type": {
                                                "value": ""
                                            },
                                            "address": {
                                                "value": "8 WEST MAYA DRIVE PHILAM HOMES QUEZON CITY",
                                                "houseNumber": "",
                                                "province": "",
                                                "street": "",
                                                "district": "",
                                                "zipCode": "",
                                                "additionalInfo": ""
                                            },
                                            "gender": {
                                                "value": ""
                                            },
                                            "idNumber": {
                                                "value": "N01-87-049586"
                                            },
                                            "placeOfBirth": {
                                                "value": ""
                                            },
                                            "placeOfIssue": {
                                                "value": ""
                                            },
                                            "yearOfBirth": {
                                                "value": "1970"
                                            },
                                            "age": {
                                                "value": ""
                                            },
                                            "fatherName": {
                                                "value": ""
                                            },
                                            "motherName": {
                                                "value": ""
                                            },
                                            "husbandName": {
                                                "value": ""
                                            },
                                            "spouseName": {
                                                "value": ""
                                            },
                                            "nationality": {
                                                "value": "PHL"
                                            },
                                            "mrzString": {
                                                "value": "",
                                                "idNumber": "",
                                                "fullName": "",
                                                "dateOfBirth": "",
                                                "dateOfExpiry": "",
                                                "gender": "",
                                                "nationality": ""
                                            },
                                            "homeTown": {
                                                "value": ""
                                            }
                                        },
                                        "croppedImageUrl": "https://sg-kyc-hyperverge-co.s3.ap-southeast-1.amazonaws.com/undefined/2023-02-24/6ad9ef/1677231200660-995d5c50-22f4-4a47-8294-06edf6a8a3c9485c32f2-04f2-480e-9eb5-7442af3c0238-4GeapMRIGR_phl_dl_1.jpg?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=ASIAXB3KY4F5L63MK6WY%2F20230224%2Fap-southeast-1%2Fs3%2Faws4_request&X-Amz-Date=20230224T093321Z&X-Amz-Expires=900&X-Amz-Security-Token=IQoJb3JpZ2luX2VjEGcaDmFwLXNvdXRoZWFzdC0xIkcwRQIgYEcaqZRAxznRkiAibYBEOAG0Br6Z%2B2k4K%2F4EDOuSLw8CIQC3mpKo9vVb%2BpaRQzlVPfTDhHQ4%2BuCIXW7k1Oh2LCBY%2BCrbBAgQEAAaDDQ4NTAxODM2MjIzNCIMt3p3QKWnCzeCxcooKrgEfbKnFmInojz3HLz6eNE%2BFO9gQVdgUcTyDqvDFnZMzNd24V0aAdX9ioJp5s8MTmsfuZA8TW9wWMHBTAYvnD3RUqfVcAF9JaGu7uVnqPisZUGjg%2BWnuGE0Z8CcZr6aUUOeo3nQHe6d5L41gXt6ozDdcltdgh54ihNPm4YeA%2F3rxTQJ2VFzRENXvxKG%2FG4uiKQW%2FlPtoqOGTrf%2FoyZD9oBnoYLd2sRzHt2530Xez37DVHtJOyhRcRd2cKIh47GU46CZVxuvxYYdeewjwJRD%2B1AflUGE4wZ5Pm0euG%2FMyhHA0tXK8ThEfeL6TaJ4zaHd6kgXLlIbARaJvIDCjXZmMv3c6GQ3T%2Fp1iayVWtM7YW0XhMZhmDSbVwiTVFGk3Gntaj%2B3AgjPS0AngzvjY2Rhpuui6yl2%2FvwhH02GVZeDB6oTQgGhxjuF8MmVYjgNr0iDiVbuCIXHI1WNbxdR9DpviiqGUeb0YSMVQvJoJ0ukA%2FYAzcFcEBeesBlSo95ykW5Bob2D5LKOAslTQsGAixdE%2FQDzsL9wS8AALbc%2Fq23HJkpb105wrS%2B8z76MB2Pax2xlgvtkZUgJOtNU85fnpLwm7ZJlUzczkR0WVSZ6jFK%2FKzJTckzMOGh7%2BrDqL8vqunghPliAtetV6y1eB4tpD%2BafooVy25pWLAd%2FVmKCunoRsC9Fa8%2Bg3Dz2cnsjhOKxr5l2XdZYl7Rl7grvjUBnQq30lIqCbYpbDOr%2FIGF5LAXu0tpDESe619R5MK8cMDDZsuGfBjqpAeyDEEEcB2aNIPmRMsJC9v56teWkKnyhiapVWPguJKGiSIMG57Sj6e4bG7mO%2FKozroeG6hhXUfhdzbWAHN92a7XIoyvG%2FEwMqX5PP8LSxZZ%2FonCeHISMBUvFkla29d8X7C%2B0%2B8b90EOfqzrtnbEQddg2%2FkAWQBPHf0qghMBrCJGYnoKkrr0QoMZBmspJigDBRB2P0nZr9wRRIzWcnZLXjIbT7hmMNtFv0Es%3D&X-Amz-Signature=1dac3a8e502fe57f8316df3328cb695e15ce6d6aa80be6868c6e3a8205d50191&X-Amz-SignedHeaders=host",
                                        "qualityChecks": {
                                            "blur": {
                                                "value": "yes"
                                            },
                                            "glare": {
                                                "value": "no"
                                            },
                                            "blackAndWhite": {
                                                "value": "no"
                                            },
                                            "capturedFromScreen": {
                                                "value": "no"
                                            },
                                            "partialId": {
                                                "value": "no"
                                            }
                                        }
                                    }
                                ],
                                "summary": {
                                    "action": "retake",
                                    "details": [
                                        {
                                            "code": "127",
                                            "message": "Document blurred"
                                        }
                                    ],
                                    "retakeMessage": "Document blurred"
                                }
                            }
                        }
                    },
                    {
                        "module": "undefined front",
                        "countrySelected": "phl",
                        "documentSelected": "dl",
                        "attempts": "2",
                        "expectedDocumentSide": "front",
                        "moduleId": "module_id",
                        "croppedImageUrl": "https://prod-audit-portal-sgp.s3.ap-southeast-1.amazonaws.com/gkyc-ap-southeast-1/readId/2023-02-24/12dqkm/1677231216715-f9183f16-d26c-492c-84b8-d89a19025c31/cropped.jpeg?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=ASIAZRKK5ZMRZ3IN3VWS%2F20230224%2Fap-southeast-1%2Fs3%2Faws4_request&X-Amz-Date=20230224T121750Z&X-Amz-Expires=900&X-Amz-Security-Token=IQoJb3JpZ2luX2VjEGwaCmFwLXNvdXRoLTEiRjBEAiAU9QkLu6LeGVALccMsO5LeSqgnGH1UWJpgvQSty8uyPwIgAklKiz0%2F8Sij2YvWNFTAMMorMl7ssS87AYZLwHyJeAcqvwUIFRABGgw2NTU2NzY1MjUzNDciDM3VbaaaWD8Xi%2FKTXiqcBePfcoo%2FmE33G5Y9Tyk1mqcJfEa1Ufgt9bi%2FKPGKqebTgdyMrG6bGZGn%2BcB2xyZkztmUJ%2ByzZeaBG9stEZKwuEZFyQxXw6kF2iSIkWZq3%2F7ZThb9FDhMP0DjYMEdimfQO45BSbsJAZ%2BTEV1lttF1qOOUrXSOs5W%2BCJhW06RgeiPh6xzCoDv236MAswmsEE%2BWzc6ca28Eeu9d71Mas0P%2FJti0pktsd%2BZbIG0PRkfrOGyaS3EXRbZV4Ju5JOAhyq%2FaMoPPPSi35XIOiRGCOyo7y2TX1y6Qra%2BAmVFlFGqNrXBbU6w0Iht%2F%2B5PfYJsrCxMCSCNZuqCwLuGU5J7EN1u5FRyYipNWo%2FujTLeeuX4r1xWyx2iIz1ZrKVMOT5%2B%2Fkmjl7Rgtzf1DluF4OMmwn942m%2BNS8niMahYODiA2%2FuC9b1DlIJ7bSk4XzavkkxtbhTNOXOs5q2dm%2Bl92ZFb9oNxJClV1bXh36ML8XKMbJH7CI62bnm4O5PPMVfKiHPrsGrbSmNylHAc2lgWuMaMAN1hsePBp%2Fv%2FFe8drrYs5Nskvn1RuDFEN76xW59kXKikz2%2FtFD5UkPZrBJ2F2qYCaD6FjjRpL4VNoObfMygtYBvqCdPH8JZSa3fAWlYp%2FngnGBAYbjdPV0Y6oYOqd5sHrf8z0qW9aFAYgkwWoEH%2B6ExkdW0IQ5QHr1w%2FB8D2gIcg2AuyFRBtkV5Dq0spy6R8nt%2FTKneq8RxONnEK6EuxkhbPqMz3yka2lN%2F3E2r3gkSWE33nobIRR0dZNVK8g1o2AdWKDR8KBr%2FOqANI0ITQV306bYZAvJwctyZcxxRfvcQuuPQi0KJs9cA3%2Bfw55zX5jX4cbz6u9c3FLK35BHR2Qh%2B%2B68BOCSbVXXZWfn9D5dbdFMNzM4p8GOrIBaFnGHF3wAAOAQ8K6ZRHvQ6%2FQysVnYZlQihROYF%2FWDWPOUrPbUGij%2FT1zb%2F1L42ifBOWTdcJYhSDxXX%2FKAtOiyl3%2B1zlYKVyjwe84g81wcBF8q7ancEX7XUtnVGqCRgdWlkwcq6wqX%2FY6GGLNtH3QfNOmpTmZ456Gf7Wa%2Ffu4enLNEGOgD2TzODD9J8AjS94o9xTvkP%2FCqZKgikDO3NvRxnULHfTeiF%2Fo7ukCFIgfr3twsA%3D%3D&X-Amz-Signature=dc26b51a73c64fe249d42e68e32420e84c4d7c46aa3918ae1aa49eea33154272&X-Amz-SignedHeaders=host",
                        "imageUrl": "https://prod-audit-portal-sgp.s3.ap-southeast-1.amazonaws.com/gkyc-ap-southeast-1/readId/2023-02-24/12dqkm/1677231216715-f9183f16-d26c-492c-84b8-d89a19025c31/image.jpeg?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=ASIAZRKK5ZMRZ3IN3VWS%2F20230224%2Fap-southeast-1%2Fs3%2Faws4_request&X-Amz-Date=20230224T121750Z&X-Amz-Expires=900&X-Amz-Security-Token=IQoJb3JpZ2luX2VjEGwaCmFwLXNvdXRoLTEiRjBEAiAU9QkLu6LeGVALccMsO5LeSqgnGH1UWJpgvQSty8uyPwIgAklKiz0%2F8Sij2YvWNFTAMMorMl7ssS87AYZLwHyJeAcqvwUIFRABGgw2NTU2NzY1MjUzNDciDM3VbaaaWD8Xi%2FKTXiqcBePfcoo%2FmE33G5Y9Tyk1mqcJfEa1Ufgt9bi%2FKPGKqebTgdyMrG6bGZGn%2BcB2xyZkztmUJ%2ByzZeaBG9stEZKwuEZFyQxXw6kF2iSIkWZq3%2F7ZThb9FDhMP0DjYMEdimfQO45BSbsJAZ%2BTEV1lttF1qOOUrXSOs5W%2BCJhW06RgeiPh6xzCoDv236MAswmsEE%2BWzc6ca28Eeu9d71Mas0P%2FJti0pktsd%2BZbIG0PRkfrOGyaS3EXRbZV4Ju5JOAhyq%2FaMoPPPSi35XIOiRGCOyo7y2TX1y6Qra%2BAmVFlFGqNrXBbU6w0Iht%2F%2B5PfYJsrCxMCSCNZuqCwLuGU5J7EN1u5FRyYipNWo%2FujTLeeuX4r1xWyx2iIz1ZrKVMOT5%2B%2Fkmjl7Rgtzf1DluF4OMmwn942m%2BNS8niMahYODiA2%2FuC9b1DlIJ7bSk4XzavkkxtbhTNOXOs5q2dm%2Bl92ZFb9oNxJClV1bXh36ML8XKMbJH7CI62bnm4O5PPMVfKiHPrsGrbSmNylHAc2lgWuMaMAN1hsePBp%2Fv%2FFe8drrYs5Nskvn1RuDFEN76xW59kXKikz2%2FtFD5UkPZrBJ2F2qYCaD6FjjRpL4VNoObfMygtYBvqCdPH8JZSa3fAWlYp%2FngnGBAYbjdPV0Y6oYOqd5sHrf8z0qW9aFAYgkwWoEH%2B6ExkdW0IQ5QHr1w%2FB8D2gIcg2AuyFRBtkV5Dq0spy6R8nt%2FTKneq8RxONnEK6EuxkhbPqMz3yka2lN%2F3E2r3gkSWE33nobIRR0dZNVK8g1o2AdWKDR8KBr%2FOqANI0ITQV306bYZAvJwctyZcxxRfvcQuuPQi0KJs9cA3%2Bfw55zX5jX4cbz6u9c3FLK35BHR2Qh%2B%2B68BOCSbVXXZWfn9D5dbdFMNzM4p8GOrIBaFnGHF3wAAOAQ8K6ZRHvQ6%2FQysVnYZlQihROYF%2FWDWPOUrPbUGij%2FT1zb%2F1L42ifBOWTdcJYhSDxXX%2FKAtOiyl3%2B1zlYKVyjwe84g81wcBF8q7ancEX7XUtnVGqCRgdWlkwcq6wqX%2FY6GGLNtH3QfNOmpTmZ456Gf7Wa%2Ffu4enLNEGOgD2TzODD9J8AjS94o9xTvkP%2FCqZKgikDO3NvRxnULHfTeiF%2Fo7ukCFIgfr3twsA%3D%3D&X-Amz-Signature=24ffce894066f7e83efe16328c3fff93fbde0aabae8e2a878707db2c13bfcf7a&X-Amz-SignedHeaders=host",
                        "apiResponse": {
                            "status": "success",
                            "statusCode": 200,
                            "metadata": {
                                "requestId": "1677231216715-f9183f16-d26c-492c-84b8-d89a19025c31",
                                "transactionId": "$transactionId"
                            },
                            "result": {
                                "details": [
                                    {
                                        "idType": "phl_dl",
                                        "fieldsExtracted": {
                                            "firstName": {
                                                "value": ""
                                            },
                                            "middleName": {
                                                "value": ""
                                            },
                                            "lastName": {
                                                "value": ""
                                            },
                                            "fullName": {
                                                "value": "HURTADO, LESTER BIADORA"
                                            },
                                            "dateOfBirth": {
                                                "value": "21-04-1970"
                                            },
                                            "dateOfIssue": {
                                                "value": ""
                                            },
                                            "dateOfExpiry": {
                                                "value": "21-04-2027"
                                            },
                                            "countryCode": {
                                                "value": ""
                                            },
                                            "type": {
                                                "value": ""
                                            },
                                            "address": {
                                                "value": "8 WEST MAYA DENE PHILAM HOMES QUEZON CITY",
                                                "houseNumber": "",
                                                "province": "",
                                                "street": "",
                                                "district": "",
                                                "zipCode": "",
                                                "additionalInfo": ""
                                            },
                                            "gender": {
                                                "value": ""
                                            },
                                            "idNumber": {
                                                "value": "N01-87-049586"
                                            },
                                            "placeOfBirth": {
                                                "value": ""
                                            },
                                            "placeOfIssue": {
                                                "value": ""
                                            },
                                            "yearOfBirth": {
                                                "value": "1970"
                                            },
                                            "age": {
                                                "value": ""
                                            },
                                            "fatherName": {
                                                "value": ""
                                            },
                                            "motherName": {
                                                "value": ""
                                            },
                                            "husbandName": {
                                                "value": ""
                                            },
                                            "spouseName": {
                                                "value": ""
                                            },
                                            "nationality": {
                                                "value": "PHL"
                                            },
                                            "mrzString": {
                                                "value": "",
                                                "idNumber": "",
                                                "fullName": "",
                                                "dateOfBirth": "",
                                                "dateOfExpiry": "",
                                                "gender": "",
                                                "nationality": ""
                                            },
                                            "homeTown": {
                                                "value": ""
                                            }
                                        },
                                        "croppedImageUrl": "https://sg-kyc-hyperverge-co.s3.ap-southeast-1.amazonaws.com/undefined/2023-02-24/6ad9ef/1677231217741-fbbb917b-a3bc-460f-8176-4d78345e612195109920-7a5b-48bf-9ea3-7c75b166fbfc-olPSISZ4bp_phl_dl_1.jpg?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=ASIAXB3KY4F5HQFS6SM7%2F20230224%2Fap-southeast-1%2Fs3%2Faws4_request&X-Amz-Date=20230224T093338Z&X-Amz-Expires=900&X-Amz-Security-Token=IQoJb3JpZ2luX2VjEGYaDmFwLXNvdXRoZWFzdC0xIkcwRQIhAIzh9a0bfnjHmcp%2BrIvs53ClDj1%2BaGnVu73XhCKrk%2B5IAiBoP6EZqieXW%2Fj9DT%2BJ5eJi3Nq0bKOL9urSGUkkh5PXayrkBAj%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F8BEAAaDDQ4NTAxODM2MjIzNCIMv6vYdt8mRSgtCLGxKrgEAMXnEQYFKTtTNZcqUR6%2Bph5M2KBTSfX0wIczAoaVgGG8aefidh%2B71RHNb%2FemeqNl1XtxeFtTB%2BQAT3BV2XEnVow4J6TF16VPjGYCu9jeZsMinddYefJwaMdVVqdvEOHQgDkMCG5XB5PVyUbz3FHlMNNaW5Hj1g0%2F%2FNLOO1jzLP7TRAtaYpJUEgCbAD5tqA3BVtqkekznzd10K9O7IOt94DbXiEJYFhdLP9F%2FOiO8%2FNR5LPPzrOPEnIFQUbw53gVBd0BvaT7DdGng3Ak6%2BxlhfwDO%2BmEe5yeUms9oeR55I2cCAXH2sZXPuM9kiQ%2FiDAQerqNyXCxfIMXGsZUV2pOeH3b15AZNSjzVOAFviF1lj4LWtF77zrQches%2B9HWd9Ro1%2FCRT1Mk0W3c9vcYZwHNcXV4yq6cKniwprXJmETpXDaIFpNZ2XYm1Z2Rz%2FVzlNSoBLwzVioFTxbMpOGmm4VUhE%2Ffdk9GhAoZzZJJY1GIgGWiMm43VFxeB%2BYskWSEbcPAgR7eh9PJSiK6xlyPSo%2FNt1pBcmZTk%2FwDfseRahJoxGEw1l1vI6GXT8mR2V1dv9FPPiwIA%2BcVdR%2BgqaT7zfOn2S%2B%2FCiZ03VGItHMbUm2ZmJCZbS9EuIS%2B1qRyoBg02N3RdUOuzMasOfJQLK7HDUoWNBHfwGKwjASqdWKMCpAekZHYh0HYyev6c5apAQIIK01V%2F2%2BzPLZ7J4TLMkfGWSu20uwyTvJ30vyZ%2BUuIu3nT6O3f6Bcy5zYxpjDCyqeGfBjqpAYkxI%2F0Ad8YB2JyxmmpL3dIZD%2FQBS1wa2QhCt074fN8TFPUE%2B%2FwIQcMqqCgQpbRfgzPkZN1ahjachvwMqY2cPzaY%2B%2BNYymxafbQV5Z4x0oT1Pqme1DyPMDhHKWZdqpkaCvMtCVRHre7TyePUM%2FrJnG3wsyb3G7Zf0MQIwgqGAOEjSCqAzmZkLjkRKSve3s6QZSxzX6WgCQFaLbvpTeGEcR37srq70J%2FQqME%3D&X-Amz-Signature=d8ce8796d13b52b6168414c0037d716109732b82ffc664f0028cf622d3c652fa&X-Amz-SignedHeaders=host",
                                        "qualityChecks": {
                                            "blur": {
                                                "value": "yes"
                                            },
                                            "glare": {
                                                "value": "no"
                                            },
                                            "blackAndWhite": {
                                                "value": "no"
                                            },
                                            "capturedFromScreen": {
                                                "value": "no"
                                            },
                                            "partialId": {
                                                "value": "no"
                                            }
                                        }
                                    }
                                ],
                                "summary": {
                                    "action": "retake",
                                    "details": [
                                        {
                                            "code": "127",
                                            "message": "Document blurred"
                                        }
                                    ],
                                    "retakeMessage": "Document blurred"
                                }
                            }
                        }
                    }
                ]
            },
            {
                "attempts": "1",
                "moduleId": "module_selfie",
                "imageUrl": "https://prod-audit-portal-sgp.s3.ap-southeast-1.amazonaws.com/gkyc-ap-southeast-1/checkLiveness/2023-02-24/12dqkm/1677231252911-e30a99cf-c625-44bf-aa3a-c6a67b42b4e0/image.jpeg?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=ASIAZRKK5ZMRZ3IN3VWS%2F20230224%2Fap-southeast-1%2Fs3%2Faws4_request&X-Amz-Date=20230224T121749Z&X-Amz-Expires=900&X-Amz-Security-Token=IQoJb3JpZ2luX2VjEGwaCmFwLXNvdXRoLTEiRjBEAiAU9QkLu6LeGVALccMsO5LeSqgnGH1UWJpgvQSty8uyPwIgAklKiz0%2F8Sij2YvWNFTAMMorMl7ssS87AYZLwHyJeAcqvwUIFRABGgw2NTU2NzY1MjUzNDciDM3VbaaaWD8Xi%2FKTXiqcBePfcoo%2FmE33G5Y9Tyk1mqcJfEa1Ufgt9bi%2FKPGKqebTgdyMrG6bGZGn%2BcB2xyZkztmUJ%2ByzZeaBG9stEZKwuEZFyQxXw6kF2iSIkWZq3%2F7ZThb9FDhMP0DjYMEdimfQO45BSbsJAZ%2BTEV1lttF1qOOUrXSOs5W%2BCJhW06RgeiPh6xzCoDv236MAswmsEE%2BWzc6ca28Eeu9d71Mas0P%2FJti0pktsd%2BZbIG0PRkfrOGyaS3EXRbZV4Ju5JOAhyq%2FaMoPPPSi35XIOiRGCOyo7y2TX1y6Qra%2BAmVFlFGqNrXBbU6w0Iht%2F%2B5PfYJsrCxMCSCNZuqCwLuGU5J7EN1u5FRyYipNWo%2FujTLeeuX4r1xWyx2iIz1ZrKVMOT5%2B%2Fkmjl7Rgtzf1DluF4OMmwn942m%2BNS8niMahYODiA2%2FuC9b1DlIJ7bSk4XzavkkxtbhTNOXOs5q2dm%2Bl92ZFb9oNxJClV1bXh36ML8XKMbJH7CI62bnm4O5PPMVfKiHPrsGrbSmNylHAc2lgWuMaMAN1hsePBp%2Fv%2FFe8drrYs5Nskvn1RuDFEN76xW59kXKikz2%2FtFD5UkPZrBJ2F2qYCaD6FjjRpL4VNoObfMygtYBvqCdPH8JZSa3fAWlYp%2FngnGBAYbjdPV0Y6oYOqd5sHrf8z0qW9aFAYgkwWoEH%2B6ExkdW0IQ5QHr1w%2FB8D2gIcg2AuyFRBtkV5Dq0spy6R8nt%2FTKneq8RxONnEK6EuxkhbPqMz3yka2lN%2F3E2r3gkSWE33nobIRR0dZNVK8g1o2AdWKDR8KBr%2FOqANI0ITQV306bYZAvJwctyZcxxRfvcQuuPQi0KJs9cA3%2Bfw55zX5jX4cbz6u9c3FLK35BHR2Qh%2B%2B68BOCSbVXXZWfn9D5dbdFMNzM4p8GOrIBaFnGHF3wAAOAQ8K6ZRHvQ6%2FQysVnYZlQihROYF%2FWDWPOUrPbUGij%2FT1zb%2F1L42ifBOWTdcJYhSDxXX%2FKAtOiyl3%2B1zlYKVyjwe84g81wcBF8q7ancEX7XUtnVGqCRgdWlkwcq6wqX%2FY6GGLNtH3QfNOmpTmZ456Gf7Wa%2Ffu4enLNEGOgD2TzODD9J8AjS94o9xTvkP%2FCqZKgikDO3NvRxnULHfTeiF%2Fo7ukCFIgfr3twsA%3D%3D&X-Amz-Signature=03315d37381d07de45e4e70d1d8a9ac8c42fec7ae924e216297eb1514a77b987&X-Amz-SignedHeaders=host",
                "apiResponse": {
                    "status": "success",
                    "statusCode": 200,
                    "metadata": {
                        "requestId": "1677231252911-e30a99cf-c625-44bf-aa3a-c6a67b42b4e0",
                        "transactionId": "$transactionId"
                    },
                    "result": {
                        "details": {
                            "liveFace": {
                                "value": "yes",
                                "confidence": "high"
                            },
                            "qualityChecks": {
                                "blur": {
                                    "value": "no",
                                    "confidence": "high"
                                },
                                "eyesClosed": {
                                    "value": "no",
                                    "confidence": "high"
                                },
                                "maskPresent": {
                                    "value": "no",
                                    "confidence": "high"
                                },
                                "multipleFaces": {
                                    "value": "no",
                                    "confidence": "high"
                                }
                            }
                        },
                        "summary": {
                            "action": "pass",
                            "details": []
                        }
                    }
                },
                "previousAttempts": []
            },
            {
                "moduleId": "module_facematch",
                "idImageUrl": "https://prod-audit-portal-sgp.s3.ap-southeast-1.amazonaws.com/gkyc-ap-southeast-1/matchFace/2023-02-24/12dqkm/1677231255988-c8601149-b518-4fa7-89b6-33f3b1eade04/id.jpeg?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=ASIAZRKK5ZMRZ3IN3VWS%2F20230224%2Fap-southeast-1%2Fs3%2Faws4_request&X-Amz-Date=20230224T121750Z&X-Amz-Expires=900&X-Amz-Security-Token=IQoJb3JpZ2luX2VjEGwaCmFwLXNvdXRoLTEiRjBEAiAU9QkLu6LeGVALccMsO5LeSqgnGH1UWJpgvQSty8uyPwIgAklKiz0%2F8Sij2YvWNFTAMMorMl7ssS87AYZLwHyJeAcqvwUIFRABGgw2NTU2NzY1MjUzNDciDM3VbaaaWD8Xi%2FKTXiqcBePfcoo%2FmE33G5Y9Tyk1mqcJfEa1Ufgt9bi%2FKPGKqebTgdyMrG6bGZGn%2BcB2xyZkztmUJ%2ByzZeaBG9stEZKwuEZFyQxXw6kF2iSIkWZq3%2F7ZThb9FDhMP0DjYMEdimfQO45BSbsJAZ%2BTEV1lttF1qOOUrXSOs5W%2BCJhW06RgeiPh6xzCoDv236MAswmsEE%2BWzc6ca28Eeu9d71Mas0P%2FJti0pktsd%2BZbIG0PRkfrOGyaS3EXRbZV4Ju5JOAhyq%2FaMoPPPSi35XIOiRGCOyo7y2TX1y6Qra%2BAmVFlFGqNrXBbU6w0Iht%2F%2B5PfYJsrCxMCSCNZuqCwLuGU5J7EN1u5FRyYipNWo%2FujTLeeuX4r1xWyx2iIz1ZrKVMOT5%2B%2Fkmjl7Rgtzf1DluF4OMmwn942m%2BNS8niMahYODiA2%2FuC9b1DlIJ7bSk4XzavkkxtbhTNOXOs5q2dm%2Bl92ZFb9oNxJClV1bXh36ML8XKMbJH7CI62bnm4O5PPMVfKiHPrsGrbSmNylHAc2lgWuMaMAN1hsePBp%2Fv%2FFe8drrYs5Nskvn1RuDFEN76xW59kXKikz2%2FtFD5UkPZrBJ2F2qYCaD6FjjRpL4VNoObfMygtYBvqCdPH8JZSa3fAWlYp%2FngnGBAYbjdPV0Y6oYOqd5sHrf8z0qW9aFAYgkwWoEH%2B6ExkdW0IQ5QHr1w%2FB8D2gIcg2AuyFRBtkV5Dq0spy6R8nt%2FTKneq8RxONnEK6EuxkhbPqMz3yka2lN%2F3E2r3gkSWE33nobIRR0dZNVK8g1o2AdWKDR8KBr%2FOqANI0ITQV306bYZAvJwctyZcxxRfvcQuuPQi0KJs9cA3%2Bfw55zX5jX4cbz6u9c3FLK35BHR2Qh%2B%2B68BOCSbVXXZWfn9D5dbdFMNzM4p8GOrIBaFnGHF3wAAOAQ8K6ZRHvQ6%2FQysVnYZlQihROYF%2FWDWPOUrPbUGij%2FT1zb%2F1L42ifBOWTdcJYhSDxXX%2FKAtOiyl3%2B1zlYKVyjwe84g81wcBF8q7ancEX7XUtnVGqCRgdWlkwcq6wqX%2FY6GGLNtH3QfNOmpTmZ456Gf7Wa%2Ffu4enLNEGOgD2TzODD9J8AjS94o9xTvkP%2FCqZKgikDO3NvRxnULHfTeiF%2Fo7ukCFIgfr3twsA%3D%3D&X-Amz-Signature=84d7c9b5e59d881105a8e7679d5f40d8e4645f62c6d4233154fb8eda02c4cdb5&X-Amz-SignedHeaders=host",
                "selfieImageUrl": "https://prod-audit-portal-sgp.s3.ap-southeast-1.amazonaws.com/gkyc-ap-southeast-1/matchFace/2023-02-24/12dqkm/1677231255988-c8601149-b518-4fa7-89b6-33f3b1eade04/selfie.jpeg?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=ASIAZRKK5ZMRZ3IN3VWS%2F20230224%2Fap-southeast-1%2Fs3%2Faws4_request&X-Amz-Date=20230224T121750Z&X-Amz-Expires=900&X-Amz-Security-Token=IQoJb3JpZ2luX2VjEGwaCmFwLXNvdXRoLTEiRjBEAiAU9QkLu6LeGVALccMsO5LeSqgnGH1UWJpgvQSty8uyPwIgAklKiz0%2F8Sij2YvWNFTAMMorMl7ssS87AYZLwHyJeAcqvwUIFRABGgw2NTU2NzY1MjUzNDciDM3VbaaaWD8Xi%2FKTXiqcBePfcoo%2FmE33G5Y9Tyk1mqcJfEa1Ufgt9bi%2FKPGKqebTgdyMrG6bGZGn%2BcB2xyZkztmUJ%2ByzZeaBG9stEZKwuEZFyQxXw6kF2iSIkWZq3%2F7ZThb9FDhMP0DjYMEdimfQO45BSbsJAZ%2BTEV1lttF1qOOUrXSOs5W%2BCJhW06RgeiPh6xzCoDv236MAswmsEE%2BWzc6ca28Eeu9d71Mas0P%2FJti0pktsd%2BZbIG0PRkfrOGyaS3EXRbZV4Ju5JOAhyq%2FaMoPPPSi35XIOiRGCOyo7y2TX1y6Qra%2BAmVFlFGqNrXBbU6w0Iht%2F%2B5PfYJsrCxMCSCNZuqCwLuGU5J7EN1u5FRyYipNWo%2FujTLeeuX4r1xWyx2iIz1ZrKVMOT5%2B%2Fkmjl7Rgtzf1DluF4OMmwn942m%2BNS8niMahYODiA2%2FuC9b1DlIJ7bSk4XzavkkxtbhTNOXOs5q2dm%2Bl92ZFb9oNxJClV1bXh36ML8XKMbJH7CI62bnm4O5PPMVfKiHPrsGrbSmNylHAc2lgWuMaMAN1hsePBp%2Fv%2FFe8drrYs5Nskvn1RuDFEN76xW59kXKikz2%2FtFD5UkPZrBJ2F2qYCaD6FjjRpL4VNoObfMygtYBvqCdPH8JZSa3fAWlYp%2FngnGBAYbjdPV0Y6oYOqd5sHrf8z0qW9aFAYgkwWoEH%2B6ExkdW0IQ5QHr1w%2FB8D2gIcg2AuyFRBtkV5Dq0spy6R8nt%2FTKneq8RxONnEK6EuxkhbPqMz3yka2lN%2F3E2r3gkSWE33nobIRR0dZNVK8g1o2AdWKDR8KBr%2FOqANI0ITQV306bYZAvJwctyZcxxRfvcQuuPQi0KJs9cA3%2Bfw55zX5jX4cbz6u9c3FLK35BHR2Qh%2B%2B68BOCSbVXXZWfn9D5dbdFMNzM4p8GOrIBaFnGHF3wAAOAQ8K6ZRHvQ6%2FQysVnYZlQihROYF%2FWDWPOUrPbUGij%2FT1zb%2F1L42ifBOWTdcJYhSDxXX%2FKAtOiyl3%2B1zlYKVyjwe84g81wcBF8q7ancEX7XUtnVGqCRgdWlkwcq6wqX%2FY6GGLNtH3QfNOmpTmZ456Gf7Wa%2Ffu4enLNEGOgD2TzODD9J8AjS94o9xTvkP%2FCqZKgikDO3NvRxnULHfTeiF%2Fo7ukCFIgfr3twsA%3D%3D&X-Amz-Signature=2ed7c1772f0ef2c8f717e869a3499474cc830dd804876833e4de1c68386a5e05&X-Amz-SignedHeaders=host",
                "apiResponse": {
                    "status": "success",
                    "statusCode": 200,
                    "metadata": {
                        "requestId": "1677231255988-c8601149-b518-4fa7-89b6-33f3b1eade04",
                        "transactionId": "$transactionId"
                    },
                    "result": {
                        "details": {
                            "match": {
                                "value": "yes",
                                "confidence": "high"
                            }
                        },
                        "summary": {
                            "action": "pass",
                            "details": []
                        }
                    }
                },
                "attempts": "1",
                "previousAttempts": []
            }
        ]
    }
}
EOT;
    }
}
