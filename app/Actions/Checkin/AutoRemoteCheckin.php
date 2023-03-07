<?php

namespace App\Actions\Checkin;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\Checkin;
use App\Models\User;

class AutoRemoteCheckin
{
    use AsAction;

    public function handle(User $agent, string $mobile = null): Checkin
    {
        return Checkin::createAutoAssociateAgent($agent)
            ->updateRemoteGeneratedURL()
            ->updateContactPersonFromMobile($mobile)
            ->notifyAgent();
    }

    public function asJob(User $agent, string $mobile = null): void
    {
        $this->handle($agent, $mobile);
    }
}
