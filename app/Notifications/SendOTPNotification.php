<?php

namespace App\Notifications;

use LBHurtado\EngageSpark\Notifications\BaseNotification;

class SendOTPNotification extends BaseNotification
{
    public function __construct($pin)
    {
        $message = trans('domain.verify', ['pin' => $pin]);

        parent::__construct($message);
    }
}
