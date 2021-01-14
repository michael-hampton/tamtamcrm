@component('mail::message')

{{ $body }}

Thanks,<br>
{{ config('app.name') }}

==== Do not remove this line / 0003 ====

@endcomponent
