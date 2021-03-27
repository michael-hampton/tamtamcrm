<div id="header" class="border-bottom {{ isset($logo) ? 'p-4' : '' }} d-flex justify-content-center">
    @isset($logo)
        <img src="{{ $logo }}" style="height: 6rem;">
    @endisset
</div>
