<?php

namespace App\Traits;

trait HasMobile
{
    public function initializeHasMobile()
    {
        $this->fillable = array_merge(
            $this->fillable, [
                'mobile'
            ]
        );

        $this->casts = array_merge(
            $this->casts, [
                'mobile_verified_at' => 'datetime',
            ]
        );
    }

    public function routeNotificationForEngageSpark()
    {
        $field = config('engagespark.notifiable.route');

        return $this->{$field};
    }
}
