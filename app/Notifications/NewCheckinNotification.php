<?php

namespace App\Notifications;

use LBHurtado\EngageSpark\Notifications\BaseNotification;

class NewCheckinNotification extends BaseNotification
{
    public function __construct($url)
    {
        $message = trans('domain.new-checkin', ['url' => $url]);

        parent::__construct($message);
    }
}
