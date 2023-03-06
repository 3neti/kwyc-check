<?php

namespace App\Observers;

use App\Models\Checkin;

class CheckinObserver
{
    /**
     * Handle the Checkin "created" event.
     *
     * @param  \App\Models\Checkin  $checkin
     * @return void
     */
    public function created(Checkin $checkin)
    {
        //
    }

    /**
     * Handle the Checkin "updated" event.
     *
     * @param  \App\Models\Checkin  $checkin
     * @return void
     */
    public function updated(Checkin $checkin)
    {
        $checkin->hydrate();
    }

    /**
     * Handle the Checkin "deleted" event.
     *
     * @param  \App\Models\Checkin  $checkin
     * @return void
     */
    public function deleted(Checkin $checkin)
    {
        //
    }

    /**
     * Handle the Checkin "restored" event.
     *
     * @param  \App\Models\Checkin  $checkin
     * @return void
     */
    public function restored(Checkin $checkin)
    {
        //
    }

    /**
     * Handle the Checkin "force deleted" event.
     *
     * @param  \App\Models\Checkin  $checkin
     * @return void
     */
    public function forceDeleted(Checkin $checkin)
    {
        //
    }
}
