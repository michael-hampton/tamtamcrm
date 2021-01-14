<!-- https://laracasts.com/discuss/channels/laravel/mail-component -->

@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            <!-- header here -->
        @endcomponent
    @endslot

    {{-- Body --}}
    <!-- Body here -->
    {{ $user }}
     trans('texts.confirmation_message')

     @component('mail::button', ['url' => url("/user/confirm/{$user->confirmation_code}")])
         trans('texts.confirm')
     @endcomponent

    {{-- Subcopy --}}
    @slot('subcopy')
        @component('mail::subcopy')
             {{ $subcopy }}
        @endcomponent
    @endslot


    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
             © {{ date('Y') }} MyComp
        @endcomponent
    @endslot
@endcomponent
