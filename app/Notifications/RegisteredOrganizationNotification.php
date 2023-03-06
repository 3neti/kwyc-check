<?php

namespace App\Notifications;

use LBHurtado\EngageSpark\Notifications\BaseNotification;
use App\Models\Organization;
use App\Models\Voucher;
use App\Models\Campaign;

class RegisteredOrganizationNotification extends BaseNotification
{
    /** @var Voucher */
    public Voucher $voucher;

    /** @var Campaign */
    protected Campaign $campaign;

    public function __construct(Voucher $voucher)
    {
        $this->setVoucher($voucher);
    }

    protected function setVoucher(Voucher $voucher)
    {
        $this->voucher = $voucher;
        $this->message = trans('domain.org-campaign', [
            'org' => rtrim($this->getOrg()->name, '.'),
            'url' => $this->getURL()
        ]);
    }

    public function getCampaign(): Campaign
    {
        return $this->voucher->campaigns->first();
    }

    protected function getOrg(): Organization
    {
        return $this->getCampaign()->repository->organization;
    }

    protected function getCode(): string
    {
        return $this->voucher->code;
    }

    protected function getURL(): String
    {
        return route('create-recruit', ['voucher' => $this->getCode()]);
    }
}
