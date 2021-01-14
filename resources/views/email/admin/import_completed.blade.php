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
            @component('email.components.header', ['p' => $data['message'], 'logo' => isset($data['logo']) ? $data['logo'] : ''])

                @if(isset($data['title']))
                    {{$data['title']}}
                @endif

            @endcomponent

            @if(!empty($data['errors']['duplicates']))
                @foreach($data['errors']['duplicates'] as $duplicate)
                    <div class="alert alert-danger mt-2">{{ $duplicate }}</div>
                @endforeach;
            @endif

            @if(!empty($data['errors']['required_headers']))
                @foreach($data['errors']['required_headers'] as $required)
                    <div class="alert alert-danger mt-2">{{ $required }}</div>
                @endforeach;
            @endif

            @if(!empty($data['errors']['headers']))
                @foreach($data['errors']['headers'] as $header)
                    <div class="alert alert-danger mt-2">{{ $header }}</div>
                @endforeach;
            @endif

            @if(!empty($data['errors']['data']))
                @foreach($data['errors']['data'] as $data)
                    <div class="alert alert-danger mt-2">{{ $data }}</div>
                @endforeach;
            @endif

            @if(!empty($data['button_text']) && !empty($data['url']))
                @component('email.components.button', ['url' => $data['url']])
                    {{$data['button_text']}}
                @endcomponent
            @endif


            @component('email.components.footer')
                Thanks
            @endcomponent
        </div>
    </div>
</div>
</div>
</body>
</html>