<?php

namespace App\Pipes;

use Illuminate\Support\Arr;
use App\Models\Checkin;
use Closure;

class HydrateCheckinPersonData
{
    public function handle(Checkin $checkin, Closure $next)
    {
        if (!empty($checkin->data)) {
            $checkin->person->data->set(
                $checkin->idType,
                Arr::get($checkin->data, 'result.results.0.apiResponse.result.details.0.fieldsExtracted')
            );
        }

        return $next($checkin);
    }
}
