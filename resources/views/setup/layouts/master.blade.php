<?php
function isActive($route, $className = 'active')
{
    if (is_array($route)) {
        return in_array(Route::currentRouteName(), $route) ? $className : '';
    }
    if (Route::currentRouteName() == $route) {
        return $className;
    }
    if (strpos(URL::current(), $route)) {
        return $className;
    }
}
?>


        <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@if (trim($__env->yieldContent('template_title')))@yield('template_title')
        | @endif {{ trans('texts.title') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('installer/img/favicon/favicon-16x16.png') }}" sizes="16x16"/>
    <link rel="icon" type="image/png" href="{{ asset('installer/img/favicon/favicon-32x32.png') }}" sizes="32x32"/>
    <link rel="icon" type="image/png" href="{{ asset('installer/img/favicon/favicon-96x96.png') }}" sizes="96x96"/>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    @yield('style')
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
<div class="master-setup">
    <div class="box">
        <div class="header">
            <h1 class="header__title">@yield('title')</h1>
        </div>
        <ul class="step">
            <li class="step__divider"></li>
            <li class="step__item {{ isActive('LaravelInstaller::final') }}">
                <i class="step__icon fa fa-server" aria-hidden="true"></i>
            </li>
            <li class="step__divider"></li>
            <li class="step__item {{ isActive('setup.environment')}} {{ isActive('setup.environment-wizard')}} {{ isActive('setup.environment-classic')}}">
                @if(Request::is('setup/environment') || Request::is('setup/environment/wizard') || Request::is('setup/environment/classic') )
                    <a href="{{ route('setup.environment') }}">
                        <i class="step__icon fa fa-cog" aria-hidden="true"></i>
                    </a>
                @else
                    <i class="step__icon fa fa-cog" aria-hidden="true"></i>
                @endif
            </li>
            <li class="step__divider"></li>
            <li class="step__item {{ isActive('setup.permissions') }}">
                @if(Request::is('setup/permissions') || Request::is('setup/environment') || Request::is('setup/environment/wizard') || Request::is('install/environment/classic') )
                    <a href="{{ route('setup.permissions') }}">
                        <i class="step__icon fa fa-key" aria-hidden="true"></i>
                    </a>
                @else
                    <i class="step__icon fa fa-key" aria-hidden="true"></i>
                @endif
            </li>

             <li class="step__divider"></li>
            <li class="step__item {{ isActive('setup.user') }}">
                @if(Request::is('setup/user'))
                    <a href="{{ route('setup.user') }}">
                        <i class="step__icon fa fa-key" aria-hidden="true"></i>
                    </a>
                @else
                    <i class="step__icon fa fa-key" aria-hidden="true"></i>
                @endif
            </li>

            <li class="step__divider"></li>
            <li class="step__item {{ isActive('setup.requirements') }}">
                @if(Request::is('install') || Request::is('setup/requirements') || Request::is('install/permissions') || Request::is('install/environment') || Request::is('install/environment/wizard') || Request::is('install/environment/classic') )
                    <a href="{{ route('setup.requirements') }}">
                        <i class="step__icon fa fa-list" aria-hidden="true"></i>
                    </a>
                @else
                    <i class="step__icon fa fa-list" aria-hidden="true"></i>
                @endif
            </li>
            <li class="step__divider"></li>
            <li class="step__item {{ isActive('setup.welcome') }}">
                @if(Request::is('install') || Request::is('setup/requirements') || Request::is('install/permissions') || Request::is('install/environment') || Request::is('install/environment/wizard') || Request::is('install/environment/classic') )
                    <a href="{{ route('setup.welcome') }}">
                        <i class="step__icon fa fa-home" aria-hidden="true"></i>
                    </a>
                @else
                    <i class="step__icon fa fa-home" aria-hidden="true"></i>
                @endif
            </li>
            <li class="step__divider"></li>
        </ul>
        <div class="main">
            @if (session('message'))
                <p class="alert text-center">
                    <strong>
                        @if(is_array(session('message')))
                            {{ session('message')['message'] }}
                        @else
                            {{ session('message') }}
                        @endif
                    </strong>
                </p>
            @endif
            @if(session()->has('errors'))
                <div class="alert alert-danger" id="error_alert">
                    <button type="button" class="close" id="close_alert" data-dismiss="alert" aria-hidden="true">
                        <i class="fa fa-close" aria-hidden="true"></i>
                    </button>
                    <h4>
                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                        {{ trans('texts.forms.errorTitle') }}
                    </h4>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('container')
        </div>
    </div>
</div>
@yield('scripts')
<script type="text/javascript">
    var x = document.getElementById('error_alert');
    var y = document.getElementById('close_alert');
    y.onclick = function () {
        x.style.display = "none";
    };
</script>
</body>
</html>
