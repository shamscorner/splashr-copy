<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Client;
use App\Models\Company;
use App\Models\Commitment;
use Illuminate\Bus\Queueable;
use App\Events\CompanyAuthorized;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class AuthorizeCompany implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $commitment;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Commitment $commitment)
    {
        $this->commitment = $commitment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // get the company
        $company = Company::findOrFail($this->commitment->company_id);

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
