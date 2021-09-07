<?php

namespace App\Listeners;

use App\Models\Company;
use App\Events\CompanyCreditCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class IncrementAvailableCreditNumber implements ShouldQueue
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
     * @param  CompanyCreditCreated  $event
     * @return void
     */
    public function handle(CompanyCreditCreated $event)
    {
        // increment the available credits count in companies table
        Company::where('id', $event->companyCredit->company_id)
            ->increment('available_credits_count', $event->companyCredit->quantity);
    }
}
