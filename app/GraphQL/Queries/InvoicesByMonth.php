<?php

namespace App\GraphQL\Queries;

use App\Services\Admin\AdminService;

class InvoicesByMonth
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        return (new AdminService())->getInvoicesList($args['month']);
    }
}
