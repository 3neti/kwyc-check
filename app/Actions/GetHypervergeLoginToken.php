<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Lorisleiva\Actions\Concerns\AsAction;

class GetHypervergeLoginToken
{
    use AsAction;

    public function handle(): ?string
    {
        $response = Http::post(config('domain.hyperverge.api.url.login'), [
            'appId' => config('domain.hyperverge.api.id'),
            'appKey' => config('domain.hyperverge.api.key'),
            'expiry' => config('domain.hyperverge.api.expiry'),
        ]);

        $token = null;
        if ($response->successful()) {
            $token = $response->json('result.token');
        }

        return $token;
    }
}
