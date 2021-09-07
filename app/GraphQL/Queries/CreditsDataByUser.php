<?php

namespace App\GraphQL\Queries;

use App\Models\Client;
use Illuminate\Support\Facades\DB;

class CreditsDataByUser
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        if (Client::where('user_id', $args['user_id'])->exists()) {
            return DB::table('credits')
                ->join('company_credit', 'credits.id', '=', 'company_credit.credit_id')
                ->where('company_credit.company_id', $args['company_id'])
                ->select('company_credit.id as id', 'type', 'quantity')
                ->get()
                ->groupBy('type')
                ->map(function ($data) {
                    return $data->sum('quantity');
                })->toJson();
        }
        return '';
    }
}
