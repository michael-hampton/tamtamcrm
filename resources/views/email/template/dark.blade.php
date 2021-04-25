@component('email.components.layout', ['background' => 'bg-dark', 'text' => 'text-white', 'border' => 'border-info'])
    {{-- Header --}}
    @slot('header')
        @component('email.components.header', ['template' => 'dark', 'logo' => isset($data['logo']) ? $data['logo'] : ''])
            @isset($data['title']))
                {{$data['title']}}
            @endisset
        @endcomponent
    @endslot

    @slot('subcopy')
        @component('email.components.subcopy')

            <!-- Body -->
            @component('email.components.message', ['content' => $data['body'], 'template' => 'dark'])

            @endcomponent

            <!-- Button -->
            @component('email.components.button', ['url' => $data['url'], 'template' => 'dark'])
                {{$data['button_text']}}
            @endcomponent

            @isset($data['signature'])
                @component('email.components.signature', ['signature' => $data['signature'], 'template' => 'dark'])

                @endcomponent
            @endisset

            {{-- Footer --}}
            @if($data['show_footer'])
                @component('email.components.footer', ['url' => 'http://tamtamcrm.github.io', 'url_text' => 'Powered by TamTam CRM', 'template' => 'dark'])
                @endcomponent
            @endif
        @endcomponent
    @endslot

@endcomponent

