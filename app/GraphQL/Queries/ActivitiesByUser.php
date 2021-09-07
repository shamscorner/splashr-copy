<?php

namespace App\GraphQL\Queries;

use App\Models\Manager;
use App\Utils\UserUtils;
use App\Services\Admin\AdminService;

class ActivitiesByUser
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $adminService = new AdminService();

        $manager = Manager::where('user_id', $args['user_id'])->first();

        if ($manager) {
            $managerId = false;

            if ($manager->role != UserUtils::ADMIN_SUPER) {
                $managerId = $manager->id;
            }

            return $adminService->getActivitiesQueryByUser($managerId);
        }

        return $adminService->getActivitiesQueryByUser(false, $args['user_id']);
    }
}
