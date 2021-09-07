<?php

namespace App\Listeners;

use App\Events\CompanyAuthorized;
use App\Models\User;
use App\Models\Client;
use App\Models\Company;
use App\Events\CompanyCreditCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AuthorizeCompanyOfClients implements ShouldQueue
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
     * @param  CreditCreated  $event
     * @return void
     */
    public function handle(CompanyCreditCreated $event)
    {
        // get the company
        $company = Company::findOrFail($event->companyCredit->company_id);

        // check whether the company is unlocked or not
        // if locked then unlock otherwise do nothing
        if (!$company->is_unlocked) {
            // unlock company
            $company->update([
                'is_unlocked' => true
            ]);

            // authorize all the users of this company who are not client
            $users = User::where('company_id', $company->id)
                ->whereDoesntHave('client')->get();

            foreach ($users as $user) {
                Client::create([
                    'user_id' => $user->id
                ]);
            }

            // create the initial google drive asset folders
            CompanyAuthorized::dispatch($company);
        }
    }
}
