<?php

namespace App\GraphQL\Mutations;

use App\Models\Commitment;

class UpdateOrCreateCommitment
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        return Commitment::updateOrCreate(
            [
                'id' => $args['id'],
                'company_id' => $args['company_id']
            ],
            $args['commitment']
        );
    }
}
