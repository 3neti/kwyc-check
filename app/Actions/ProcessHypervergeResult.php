<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use Lorisleiva\Actions\Concerns\AsAction;

class ProcessHypervergeResult
{
    use AsAction;

    public function handle(string $transactionId)
    {
        $response = Http::withHeaders([
            'appId' => config('domain.hyperverge.api.id'),
            'appKey' => config('domain.hyperverge.api.key'),
        ])->post(config('domain.hyperverge.api.url.result'), compact('transactionId'));

        $json = null;
        if ($response->successful()) {
            $json = $response->json('result.results');
        }

        return $json;
    }
}
