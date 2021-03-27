<div class="px-4 py-4 d-flex flex-column justify-content-center text-center">
    @isset($url)
        <a href="{{ $url }}" class="text-primary">
            @isset($url_text)
                {!! $url_text !!}
            @else
                {{ $url }}
            @endisset
        </a>
    @endisset
</div>