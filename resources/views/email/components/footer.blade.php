<div class="mt-4 text-center">
    <p class="text-center">{{ $slot }}</p>

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