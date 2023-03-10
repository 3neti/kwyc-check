<?php

namespace App\Actions\Checkin;

use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Pipeline\Pipeline;
use App\Models\Checkin;

class HydrateCheckinPerson
{
    use AsAction;

    protected Pipeline $pipeline;

    public function __construct(Pipeline $pipeline) {
        $this->pipeline = $pipeline;
    }

    public function handle(Checkin $checkin): bool
    {
        $checkin = $this->pipeline
            ->send($checkin)
            ->through(config('domain.checkin.person.hydrate'))
            ->thenReturn();
        $checkin->person->save();

        return (null !== $checkin->person->data);
    }

    public function asJob(Checkin $checkin)
    {
        $this->handle($checkin);
    }
}
