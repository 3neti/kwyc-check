<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use Lorisleiva\Actions\ActionRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;
use App\Models\Checkin;

class ProcessHypervergeResult
{
    use AsAction;

    public function handle(Checkin $checkin): bool
    {
        $transactionId = $checkin->uuid;
        $response = Http::withHeaders([
            'appId' => config('domain.hyperverge.api.id'),
            'appKey' => config('domain.hyperverge.api.key'),
        ])->post(config('domain.hyperverge.api.url.result'), compact('transactionId'));

        $json = null; if ($response->successful()) {
            $json = $response->json();
            $checkin->setAttribute('data', $json);
            $checkin->save();
        }

        return !is_null($json);
    }

    public function rules(): array
    {
        return [
            'transactionId' => ['required','uuid'],
            'status' => ['required']
        ];
    }

    public function asController(ActionRequest $request)
    {
        $status = Arr::get($request->all(), 'status');

        if ($status == 'auto_approved') {
            $uuid = Arr::get($request->all(), 'transactionId');
            $checkin = Checkin::find($uuid);
            self::dispatch($checkin);
        } else {
            dd($request);
        }
    }

    public function asJob(Checkin $checkin)
    {
        $this->handle($checkin);
    }
}
