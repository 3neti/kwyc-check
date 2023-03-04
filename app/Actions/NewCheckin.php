<?php

namespace App\Actions;

use App\Notifications\NewCheckinNotification;
use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\Checkin;
use App\Models\User;

class NewCheckin
{
    use AsAction;

    public function handle(User $agent): Checkin
    {
        $checkin = Checkin::make();
        $checkin->agent()->associate($agent);
        $checkin->save();
        $url = GenerateHypervergeURLLink::run($checkin->uuid);
        if ($url) {
            $checkin->setAttribute('url', $url);
        }
        $checkin->save();
        $agent->notify(new NewCheckinNotification($url));

        return $checkin;
    }
}
