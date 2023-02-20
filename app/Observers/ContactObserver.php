<?php

namespace App\Observers;

use App\Classes\Phone;
use App\Models\Contact;
use function PHPUnit\Framework\isEmpty;

class ContactObserver
{
    /**
     * Handle the Contact "creating" event.
     *
     * @param  \App\Models\Contact  $contact
     * @return void
     */
    public function creating(Contact $contact)
    {
        if (! $contact->getAttribute('handle')) {
            $contact->setAttribute('handle', Phone::number($contact->getAttribute('mobile')));
        }
    }

    /**
     * Handle the Contact "created" event.
     *
     * @param  \App\Models\Contact  $contact
     * @return void
     */
    public function created(Contact $contact)
    {
        //
    }

    /**
     * Handle the Contact "updated" event.
     *
     * @param  \App\Models\Contact  $contact
     * @return void
     */
    public function updated(Contact $contact)
    {
        //
    }

    /**
     * Handle the Contact "saving" event.
     *
     * @param  \App\Models\Contact  $contact
     * @return void
     */
    public function saving(Contact $contact)
    {
        if ($contact->isDirty('mobile') && $mobile = $contact->getAttribute('mobile')) {
            $contact->setAttribute('mobile', Phone::number($mobile));
        }
    }

    /**
     * Handle the Contact "deleted" event.
     *
     * @param  \App\Models\Contact  $contact
     * @return void
     */
    public function deleted(Contact $contact)
    {
        //
    }

    /**
     * Handle the Contact "restored" event.
     *
     * @param  \App\Models\Contact  $contact
     * @return void
     */
    public function restored(Contact $contact)
    {
        //
    }

    /**
     * Handle the Contact "force deleted" event.
     *
     * @param  \App\Models\Contact  $contact
     * @return void
     */
    public function forceDeleted(Contact $contact)
    {
        //
    }
}
