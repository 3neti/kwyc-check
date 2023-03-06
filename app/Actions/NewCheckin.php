<?php

namespace App\Actions;

use App\Models\Contact;
use App\Notifications\NewCheckinNotification;
use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\Checkin;
use App\Models\User;

class NewCheckin
{
    use AsAction;

    public function handle(User $agent, string $contactMobile = null): Checkin
    {
        $checkin = Checkin::make();
        $checkin->agent()->associate($agent);

        optional($this->createContact($contactMobile), function ($contact) use ($checkin) {
            $checkin->person()->associate($contact);
        });

        $checkin->save();
        $url = GenerateHypervergeURLLink::run($checkin->uuid);
        if ($url) {
            $checkin->setAttribute('url', $url);
        }
        $checkin->save();
        $agent->notify(new NewCheckinNotification($url));

        return $checkin;
    }

    public function asJob(User $agent, string $contactMobile = null): void
    {
        $this->handle($agent, $contactMobile);
    }

    protected function createContact(string $mobile): ?Contact
    {
        if ($mobile) {
            return app(Contact::class)->create(compact('mobile'));
        }
    }
}
