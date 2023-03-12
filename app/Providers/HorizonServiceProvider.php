<?php

namespace App\Providers;

use Laravel\Horizon\HorizonApplicationServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use App\Models\User;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        // Horizon::routeSmsNotificationsTo('15556667777');
         Horizon::routeMailNotificationsTo(User::getSystem()->email);
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');

         Horizon::night();
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewHorizon', function ($user) {
            return in_array($user->email, [
                User::getSystem()->email,
            ]);
        });
    }
}
