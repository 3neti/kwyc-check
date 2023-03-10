<?php

namespace App\Pipes;

use Illuminate\Support\Str;
use App\Models\Checkin;
use Closure;

class HydrateCheckinPersonHandle
{
    public function handle(Checkin $checkin, Closure $next)
    {
        if (!empty($checkin->data)) {
            if (count($checkin->person->getAttribute('data'))>0) {
                $name = $checkin->person->data->get("{$checkin->idType}.fullName.value");
                switch ($checkin->idType) {
                    case 'passport':

                        break;
                    case 'phl_dl':
                        $name = trim(implode(" ", array_reverse(explode(",", $name))));
                        break;
                }
                $name = Str::title($name);
                $checkin->person->setAttribute('handle', $name);
            }
        }

        return $next($checkin);
    }
}
