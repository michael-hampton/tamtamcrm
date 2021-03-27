<div class="d-flex flex-column align-items-center mt-4 mb-4">
    <h2 id="title" class="mt-4 p-4">
        {{ $slot }}
    </h2>
    @isset($content)
        <p class="p-4">{{ $content }}</p>
    @endisset
</div>