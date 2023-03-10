<?php

namespace App\Actions;

use App\Actions\Checkin\HydrateCheckinPerson;
use Lorisleiva\Actions\Concerns\AsAction;
use Lorisleiva\Actions\ActionRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Support\Arr;
use App\Models\Checkin;

class ProcessHypervergeResult
{
    use AsAction;

    protected Checkin $checkin;

    protected Response $response;

    public function handle(Checkin $checkin): bool
    {
        $this->setCheckin($checkin)
            ->getData()
            ->processData()
            ->hydratePerson()
        ;

        return (null !== $this->checkin->getAttribute('data'));
    }

    #[ArrayShape(['transactionId' => "string", 'status' => "string"])]
    public function rules(): array
    {
        return [
            'transactionId' => 'required|uuid',
            'status' => 'required'
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

    protected function setCheckin(Checkin $checkin): self
    {
        $this->checkin = $checkin;

        return $this;
    }

    protected function getData(): self
    {
        $headers = [
            'appId' => config('domain.hyperverge.api.id'),
            'appKey' => config('domain.hyperverge.api.key'),
        ];
        $url = config('domain.hyperverge.api.url.result');
        $attribs = ['transactionId' => $this->checkin->getAttribute('uuid')];
        $this->response = Http::withHeaders($headers)->post($url, $attribs);

        return $this;
    }

    protected function processData(): self
    {
        if ($this->response->successful()){
            $this->checkin->setAttribute('data', $this->response->json());
            $this->checkin->save();
        }

        return $this;
    }

    protected function hydratePerson(): self
    {
        HydrateCheckinPerson::dispatch($this->checkin);

        return $this;
    }
}
