<div id="header" class="border-bottom {{ isset($logo) ? 'p-4' : '' }} d-flex justify-content-center">
    @isset($logo)
        <img src="{{ $logo }}" style="height: 6rem;">
    @endisset
</div>

<div class="d-flex flex-column align-items-center mt-4 mb-4">
    <h2 id="title" class="mt-4">
        {{ $slot }}
    </h2>
    @isset($p)
        <p>{{ $p }}</p>
    @endisset
</div>
