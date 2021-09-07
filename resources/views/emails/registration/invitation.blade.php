@component('mail::message')
# Hello!

You have got an invitation from Splashr!

@component('mail::button', ['url' => route('invite-new-user', ['token' => $token])])
Accept
@endcomponent

Thank you for using our application!<br><br>
Regards,<br>
{{ config('app.name') }}
@endcomponent