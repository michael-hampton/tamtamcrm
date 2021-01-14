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
                <div class="px-4 py-4 d-flex flex-column">
                    @isset($data['signature'])
                        <div class="px-4 py-4 d-flex flex-column align-items-center">
                            <img style="display:block; width:100px;height:100px;" id="base64image" src="{{ $data['signature'] }}"/>
                        </div>
                    @endisset


                    @if(!empty($data['button_text']) && !empty($data['url']))
                        @component('email.components.button', ['url' => $data['url']])
                            {{$data['button_text']}}
                        @endcomponent
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>  
