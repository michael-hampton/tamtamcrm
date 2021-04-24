@component('email.components.layout', ['background' => 'bg-light', 'text' => 'text-white', 'border' => 'border-info'])
    @slot('subcopy')
        @component('email.components.subcopy')

            <!-- Body -->
            @component('email.components.message', ['content' => $data['body'], 'template' => 'light'])

            @endcomponent

            <!-- Button -->
            @component('email.components.button', ['url' => $data['url'], 'template' => 'light'])
                {{$data['button_text']}}
            @endcomponent

            @isset($data['signature'])
                @component('email.components.signature', ['signature' => $data['signature'], 'template' => 'light'])

                @endcomponent
            @endisset
        @endcomponent
    @endslot

@endcomponent

