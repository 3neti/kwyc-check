<?php

namespace App\Traits;

use OTPHP\TOTPInterface;
use OTPHP\OTPInterface;
use OTPHP\Factory;
use OTPHP\TOTP;

trait Verifiable
{
    public function setTOTP(TOTPInterface $totp): self
    {
        $totp->setLabel(config('domain.otp.label'));
        $this->URI = $totp->getProvisioningUri();
        $this->save();

        return $this;
    }

    public function getTOTP(): OTPInterface
    {
        return Factory::loadFromProvisioningUri($this->URI);
    }

    public function challenge($notification = null): self
    {
        $period = config('domain.otp.period');
        tap(TOTP::create(null, $period, 'sha1', 4), function ($totp) {
            $this->setTOTP($totp);
        });

        return $this;
    }

    //TODO: change email_verified_at to verified_at
    public function verify($otp): self
    {
        $verified = $this->getTOTP()->verify($otp);

        if ($verified) $this->forceFill(['mobile_verified_at' => now()])->save();

        return $this;
    }

    public function verified(): bool
    {
        return $this->mobile_verified_at && $this->mobile_verified_at <= now();
    }
}
