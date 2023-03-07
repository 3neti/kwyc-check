<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Contact;
use App\Models\Organization;
use App\Observers\UserObserver;
use App\Observers\ContactObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use App\Observers\OrganizationObserver;
use App\Listeners\BalanceUpdatedListener;
use MOIREI\Vouchers\Events\VoucherRedeemed;
use App\Listeners\TransactionCreatedListener;
use App\Listeners\OnboardAgentToOrganization;
use Bavix\Wallet\Internal\Events\TransactionCreatedEvent;
use Bavix\Wallet\Internal\Events\BalanceUpdatedEventInterface;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The model observers for your application.
     *
     * @var array
     */
    protected $observers = [
        User::class => [UserObserver::class],
        Contact::class => [ContactObserver::class],
        Organization::class => [OrganizationObserver::class]
    ];

    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        BalanceUpdatedEventInterface::class => [
            BalanceUpdatedListener::class
        ],
        TransactionCreatedEvent::class => [
            TransactionCreatedListener::class
        ],
        VoucherRedeemed::class => [
            OnboardAgentToOrganization::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
