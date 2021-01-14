@component('mail::message')
    # Hello {{$teacher->first_name}},
    <br>
    {{$user->first_name}} {{$user->last_name}} has changed their email address.

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent