<?php

namespace App\Repositories\Manager;

use App\Models\Manager;
use Illuminate\Support\Facades\Auth;

class ManagerRepository implements ManagerRepositoryInterface
{
    /**
     * Get the authenticated manager
     * 
     * @param array $selects
     * 
     * @return App\Models\Manager
     */
    public function getAuthenticatedManager(array $selects = ['*']): Manager
    {
        return Manager::where('user_id', Auth::user()->id)->select('id', 'role')->first();
    }
}
