<?php

namespace App\GraphQL\Queries;

use App\Services\Admin\AdminService;

class FetchOrdersOfClientsOfManager
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $service = new AdminService();
        return $service->getOrdersList($args['user_id'], false, false, false, true);
    }
}
