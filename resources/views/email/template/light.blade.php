@component('email.components.layout', ['background' => 'bg-light', 'text' => 'text-white', 'border' => 'border-info'])
    {{-- Header --}}
    @slot('header')
        @component('email.components.header', ['template' => 'light', 'logo' => isset($data['logo']) ? $data['logo'] : ''])
            @if(isset($data['title']))
                {{$data['title']}}
            @endif
        @endcomponent
    @endslot

    @slot('subcopy')
        @component('email.components.subcopy')

            <!-- Body -->
            @component('email.components.message', ['content' => $data['body'], 'template' => 'light'])

            @endcomponent

            <!-- Button -->
            @isset($url)
                @component('email.components.button', ['url' => $data['url'], 'template' => 'light'])
                    {{$data['button_text']}}
                @endcomponent
            @endisset

            @isset($data['signature'])
                @component('email.components.signature', ['signature' => $data['signature'], 'template' => 'light'])

                @endcomponent
            @endisset

            {{-- Footer --}}
            @if($data['show_footer'])
                @component('email.components.footer', ['url' => 'http://tamtamcrm.github.io', 'url_text' => 'Powered by TamTam CRM', 'template' => 'light'])
                @endcomponent
            @endif
        @endcomponent
    @endslot

@endcomponent

