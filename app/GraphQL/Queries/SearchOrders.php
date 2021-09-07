<?php

namespace App\GraphQL\Queries;

use App\Services\Admin\AdminService;

class SearchOrders
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $service = new AdminService();

        $managerUserId = $service->getPermissionFilterByRole($args['user_id']);

        if (!$args['query']) {
            return $service->getOrdersList($managerUserId, false, false, false, true);
        }
        return $service->searchInOrdersList($managerUserId, $args['query']);
    }
}
