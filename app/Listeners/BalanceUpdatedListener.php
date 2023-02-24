<?php

namespace App\Listeners;

use Bavix\Wallet\Internal\Events\BalanceUpdatedEventInterface;
use Laravel\Nova\Notifications\NovaNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Bavix\Wallet\Models\Wallet;
use Brick\Money\Money;

class BalanceUpdatedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param BalanceUpdatedEventInterface $event
     * @return void
     */
    public function handle(BalanceUpdatedEventInterface $event)
    {
        $wallet = Wallet::find($event->getWalletId());
        $amount = Money::ofMinor($event->getBalance(), 'PHP')->formatTo(config('app.locale'));
        $message = "Your new balance is $amount.";
        $wallet->holder->notify(NovaNotification::make()
            ->message($message)
            ->icon('cash')
            ->type(NovaNotification::INFO_TYPE)
        );
    }
}
