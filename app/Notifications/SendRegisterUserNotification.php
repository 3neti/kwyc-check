<?php

namespace App\Notifications;

use App\Models\Organization;
use LBHurtado\EngageSpark\Notifications\BaseNotification;
use App\Models\Campaign;

class SendRegisterUserNotification extends BaseNotification
{
    /** @var Campaign */
    protected $campaign;

    public function __construct(Campaign $campaign)
    {
        $this->setCampaign($campaign);
    }

    protected function setCampaign(Campaign $campaign): void
    {
        $this->campaign = $campaign;
        $this->message = trans('domain.org-campaign', [
            'org' => $this->getOrg()->name,
            'url' => $this->getURL()
        ]);
    }

    public function getCampaign(): Campaign
    {
        return $this->campaign;
    }

    protected function getOrg(): Organization
    {
        return $this->getCampaign()->repository->organization;
    }

    protected function getURL(): String
    {
        $org_id = $this->getOrg()->id;

        return route('register-user', compact('org_id'));
    }
}
