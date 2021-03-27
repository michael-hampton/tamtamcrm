@component('email.components.layout', ['background' => 'bg-light', 'text' => 'text-dark', 'border' => 'border-info'])
    {{-- Header --}}
    @slot('header')
        @component('email.components.header', ['logo' => isset($data['logo']) ? $data['logo'] : ''])

        @endcomponent
    @endslot

    {{-- Body --}}
    <!-- Body here -->

    {{-- Subcopy --}}
    @slot('subcopy')
        @component('email.components.message', ['content' => $data['body']])
            @if(isset($data['title']))
                {{$data['title']}}
            @endif
        @endcomponent

        @component('email.components.subcopy')
            @isset($data['signature'])
                @component('email.components.signature', ['signature' => $data['signature']])

                @endcomponent
            @endisset

            @component('email.components.button', ['url' => $data['url']])
                {{$data['button_text']}}
            @endcomponent
        @endcomponent
    @endslot

    {{-- Footer --}}
    @if($data['show_footer'])
        @slot('footer')
            @component('email.components.footer', ['url' => 'http://tamtamcrm.github.io', 'url_text' => 'Powered by TamTam CRM'])
            @endcomponent
        @endslot
    @endif
@endcomponent

