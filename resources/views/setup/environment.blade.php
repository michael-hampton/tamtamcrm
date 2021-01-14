@extends('setup.layouts.master')

@section('template_title')
    {{ trans('texts.environment.menu.templateTitle') }}
@endsection

@section('title')
    <i class="fa fa-cog fa-fw" aria-hidden="true"></i>
    {!! trans('texts.environment.menu.title') !!}
@endsection

@section('container')

    <p class="text-center">
        {!! trans('texts.environment.menu.desc') !!}
    </p>
    <div class="buttons">
        <a href="{{ route('setup.environment-wizard') }}" class="button button-wizard">
            <i class="fa fa-sliders fa-fw" aria-hidden="true"></i> {{ trans('texts.environment.menu.wizard-button') }}
        </a>
        <a href="{{ route('setup.environment-classic') }}" class="button button-classic">
            <i class="fa fa-code fa-fw" aria-hidden="true"></i> {{ trans('texts.environment.menu.classic-button') }}
        </a>
    </div>

@endsection
