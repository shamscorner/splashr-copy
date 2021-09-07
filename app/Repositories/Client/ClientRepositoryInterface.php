<?php

namespace App\Repositories\Client;

use App\Models\User;
use App\Models\Client;
use App\Models\Company;
use App\Models\Video\Campaign;

interface ClientRepositoryInterface
{
    /**
     * Get the authenticated client
     * 
     * @param array $selects
     * 
     * @return App\Models\Client
     */
    public function getAuthenticatedClient(array $selects = ['*']): Client;

    /**
     * Get the client of a specified user model
     * 
     * @param string $userId
     * 
     * @return App\Models\Client
     */
    public function getClientFromUserId(string $userId): Client;

    /** 
     * Get the first campaign of the authenticated client
     * 
     * @param array $selects: ['*']
     * 
     * @return mixed
     */
    public function getFirstCampaignOfAuthenticatedClient(array $selects = ['*']);

    /**
     * Get the company of the authenticated user
     * 
     * @return App\Models\Company
     */
    public function getCompanyOfAuthenticatedUser(): Company;

    /**
     * Get the first client who created the company - owner
     * 
     * @param string $companyId
     * 
     * @return mixed
     */
    public function getTheFirstClientOfACompany(string $companyId);
}
