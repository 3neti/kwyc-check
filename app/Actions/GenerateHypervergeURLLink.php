<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Support\Facades\Http;

class GenerateHypervergeURLLink
{
    use AsAction;

    public function handle($transactionId): ?string
    {
        $workflowId = config('domain.hyperverge.url.workflow');
        $redirectUrl = route('hyperverge-result');
        $inputs = [];
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
