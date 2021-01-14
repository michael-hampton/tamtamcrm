@extends('setup.layouts.master')

@section('template_title')
    {{ trans('texts.welcome.templateTitle') }}
@endsection

@section('title')
    {{ trans('texts.welcome.title') }}
@endsection

@section('container')
    <p class="text-center">
      {{ trans('texts.welcome.message') }}
    </p>
    <p class="text-center">
      <a href="{{ route('setup.requirements') }}" class="button">
        {{ trans('texts.welcome.next') }}
        <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
      </a>
    </p>
@endsection
