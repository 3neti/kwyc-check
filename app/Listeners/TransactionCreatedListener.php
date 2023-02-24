<?php

namespace App\Listeners;

use Bavix\Wallet\Internal\Events\TransactionCreatedEventInterface;
use Laravel\Nova\Notifications\NovaNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Bavix\Wallet\Models\Transaction;
use Bavix\Wallet\Models\Transfer;
use Bavix\Wallet\Models\Wallet;
use Brick\Money\Money;

class TransactionCreatedListener
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
     * @param  \Bavix\Wallet\Internal\Events\TransactionCreatedEventInterface  $event
     * @return void
     */
    public function handle(TransactionCreatedEventInterface $event)
    {
        $transaction = Transaction::find($event->getId());
        $amount = Money::ofMinor(abs($transaction->amount), 'PHP')->formatTo(config('app.locale'));
        $verb = 'verb here';
        $wallet_direction = '';
        $source = 'the system';
        logger('start');
        logger($transaction);
        logger('end');
        switch ($event->getType()) {
            case 'deposit': {
                $verb = 'deposit by';
                $transfer = Transfer::where('deposit_id', $transaction->id)->first();
                if ($transfer) {
                    $source = "{$transfer->from->holder->name} [{$transfer->from->holder->mobile}]";
                    $verb = "$transfer->status from";
                }
                $wallet_direction = 'going to';
                break;
            }
            case 'withdraw': {
                $verb = 'withdrawal by';
                $transfer = Transfer::where('withdraw_id', $transaction->id)->first();
                if ($transfer) {
                    $source = "{$transfer->to->holder->name} [{$transfer->to->holder->mobile}]";
                    $verb = "$transfer->status to";
                }
                $wallet_direction = 'coming from';
                break;
            }
        }
        $message = "There was a $amount $verb $source $wallet_direction your wallet.";
        $transaction->payable->notify(NovaNotification::make()
            ->message($message)
            ->icon('cash')
            ->type(NovaNotification::INFO_TYPE)
        );
    }
}
