<?php

namespace App\GraphQL\Queries;

use App\Services\Admin\AdminService;

class SearchManagers
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        return (new AdminService())->searchManagers($args['query']);
    }
}
