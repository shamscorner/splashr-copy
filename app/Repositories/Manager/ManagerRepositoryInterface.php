<?php

namespace App\Repositories\Manager;

use App\Models\Manager;

interface ManagerRepositoryInterface
{
    /**
     * Get the authenticated Manager
     * 
     * @param array $selects
     * 
     * @return App\Models\Manager
     */
    public function getAuthenticatedManager(array $selects = ['*']): Manager;
}
