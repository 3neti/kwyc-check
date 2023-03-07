<?php

namespace App\Actions\Checkin;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\{ Checkin, Contact};

class AssociatePersonToCheckin
{
    use AsAction;

    public function handle(Checkin $checkin, string $mobile, $class = Contact::class)
    {
        $contact = app($class)->create(compact('mobile'));
        $checkin->person()->associate($contact);
        $checkin->save();

        return $checkin;
    }

    public function asJob(Checkin $checkin, string $mobile, $class = Contact::class)
    {
        $this->handle($checkin, $mobile, $class);
    }
}
