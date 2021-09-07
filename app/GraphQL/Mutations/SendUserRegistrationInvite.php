<?php

namespace App\GraphQL\Mutations;

use App\Models\Invitation;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationInvitationSent;

class SendUserRegistrationInvite
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        // save data
        $invitation = Invitation::create([
            'invitee_email' => $args['data']['invitee_email'],
            'inviter_id' => $args['data']['inviter_id'],
            'metadata' => json_encode($args['data']['metadata'])
        ]);

        // send mail invitation to invitee
        Mail::to($args['data']['invitee_email'])->send(new RegistrationInvitationSent([
            'sender' => $args['data']['metadata']['inviter']['email'],
            'token' => $invitation->id,
        ]));

        return $invitation;
    }
}
