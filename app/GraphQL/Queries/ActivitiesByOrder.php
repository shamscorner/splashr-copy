<?php

namespace App\GraphQL\Queries;

use App\Models\Manager;
use App\Utils\UserUtils;
use App\Services\Admin\AdminService;

class ActivitiesByOrder
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $managerId = false;

        $adminService = new AdminService();

        $manager = Manager::where('user_id', $args['user_id'])->first();

        if ($manager) {
            if ($manager->role == UserUtils::ADMIN_SUPER) {
                $managerId = false;
            } else {
                $managerId = $manager->id;
            }
        }

        return $adminService->getActivitiesQueryByOrderAndUser($managerId, $args['order_id']);
    }
}
