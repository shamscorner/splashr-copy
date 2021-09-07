<?php

namespace App\GraphQL\Queries;

use App\Services\Admin\AdminService;

class FetchCompaniesDataForClientsOfManager
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $service = new AdminService();
        return $service->getCompaniesList($args['user_id']);
    }
}
