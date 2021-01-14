<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>


<body class="bg-secondary my-4 text-white">
<div class="row">
    <div class="col-8 offset-2">
        <div style="border-width: 4px !important;" class="bg-dark shadow border-top border-info">
            @component('email.components.header', ['p' => '', 'logo' => $url])
                @lang('texts.download')
            @endcomponent


            @component('email.components.footer')
                Thanks
            @endcomponent
        </div>
    </div>
</div>
</div>
</body>
</html>