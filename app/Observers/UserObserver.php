<?php

namespace App\Observers;

use App\Classes\Phone;
use App\Models\User;
use Laravel\Nova\Notifications\NovaNotification;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        $user->createWallet(config('wallet.wallet.default'));

//        foreach (User::all() as $u) {
//            $u->notify(NovaNotification::make()
//                ->message('New User: ' . $user->name)
//                ->icon('user')
//                ->type('success')
//            );
//        }
    }

    public function saving(User $user)
    {
        if ($user->isDirty('mobile') && $mobile = $user->getAttribute('mobile')) {
            $user->setAttribute('mobile', Phone::number($mobile));
        }
    }
}
