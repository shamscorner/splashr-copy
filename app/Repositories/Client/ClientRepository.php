<?php

namespace App\Repositories\Client;

use App\Models\User;
use App\Models\Client;
use App\Models\Company;
use App\Models\Video\Campaign;
use Illuminate\Support\Facades\Auth;

class ClientRepository implements ClientRepositoryInterface
{
    /**
     * Get the authenticated client
     * 
     * @param array $selects
     * 
     * @return App\Models\Client
     */
    public function getAuthenticatedClient(array $selects = ['*']): Client
    {
        return Client::where('user_id', Auth::user()->id)->select('id')->first();
    }

    /**
     * Get the client of a specified user model
     * 
     * @param string $userId
     * 
     * @return App\Models\Client
     */
    public function getClientFromUserId(string $userId): Client
    {
        return Client::where('user_id', $userId)->first();
    }

    /** 
     * Get the first campaign of the authenticated client
     * 
     * @param array $selects: ['*']
     * 
     * @return mixed
     */
    public function getFirstCampaignOfAuthenticatedClient(array $selects = ['*'])
    {
        return $this->getAuthenticatedClient($selects)->campaigns()->first();
    }

    /**
     * Get the company of the authenticated user
     * 
     * @return App\Models\Company
     */
    public function getCompanyOfAuthenticatedUser(): Company
    {
        return Company::where('id', Auth::user()->company_id)->first();
    }

    /**
     * Get the first client who created the company - owner
     * 
     * @param string $companyId
     * 
     * @return mixed
     */
    public function getTheFirstClientOfACompany(string $companyId)
    {
        return Client::where('user_id', '=', function ($query) use ($companyId) {
            $query->select('users.id')
                ->from('users')
                ->where('users.company_id', $companyId)
                ->orderBy('users.created_at')
                ->limit(1);
        })->first();
    }
}
