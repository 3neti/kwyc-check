<?php

namespace App\Traits;

use App\Classes\Phone;
use Illuminate\Database\Eloquent\Builder;

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

    public function scopeWithMobile(Builder $query, string $mobile): void
    {
        $query->where('mobile', Phone::number($mobile));
    }

    static public function fromMobile($mobile): ?self
    {
        return self::withMobile($mobile)->first();
    }


}
