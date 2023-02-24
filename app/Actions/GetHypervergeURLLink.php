<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use Lorisleiva\Actions\Concerns\AsAction;

class GetHypervergeURLLink
{
    use AsAction;

    public function handle(string $transactionId, array $inputs = []): ?string
    {
        $workflowId = config('domain.hyperverge.url.workflow');
        $redirectUrl = config('domain.hyperverge.url.redirect');
        $inputs = array_merge(['app' => config('app.name')], $inputs);
        $languages = ['en' => 'English'];
        $defaultLanguage = 'en';
        $expiry = config('domain.hyperverge.api.expiry');
        $response = Http::withHeaders([
            'appId' => config('domain.hyperverge.api.id'),
            'appKey' => config('domain.hyperverge.api.key'),
        ])->post(config('domain.hyperverge.api.url.kyc'),
            compact('transactionId', 'workflowId', 'redirectUrl', 'inputs', 'languages', 'defaultLanguage', 'expiry')
        );
        $url = null;
        if ($response->successful()) {
            $url = $response->json('result.startKycUrl');
        }

        return $url;
    }
}
