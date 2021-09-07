<?php

namespace App\GraphQL\Mutations;

use App\Models\Video\RememberedAnswer;

class RememberAnswers
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $data = RememberedAnswer::where('client_id', $args['answers']['client_id'])->first();

        if ($data) {
            // data from db
            $decodedData = json_decode($data->answers, true);
            // data from input
            $decodedArgs = json_decode($args['answers']['answers'], true);
            // extract keys
            $keysOfDecodedArgs = array_keys($decodedArgs);

            foreach ($keysOfDecodedArgs as $keyArg) {
                $decodedData[$keyArg] = $decodedArgs[$keyArg];
            }

            // update
            $data->answers = json_encode($decodedData);
            $data->save();

            return $data;
        }

        return RememberedAnswer::create([
            'client_id' => $args['answers']['client_id'],
            'answers' => $args['answers']['answers']
        ]);
    }
}
