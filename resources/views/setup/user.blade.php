@extends('setup.layouts.master')

@section('template_title')
    {{ trans('texts.environment.classic.templateTitle') }}
@endsection

@section('title')
    <i class="fa fa-code fa-fw" aria-hidden="true"></i> {{ trans('texts.user.title') }}
@endsection

@section('container')

    <form method="post" action="{{ route('setup.saveUser') }}">
        {!! csrf_field() !!}
        <div class="form-group {{ $errors->has('firstname') ? ' has-error ' : '' }}">
            <label for="firstname">
                {{ trans('texts.user.form.firstname_label') }}
            </label>
            <input type="text" name="first_name" id="firstname" value=""
                   placeholder="{{ trans('texts.user.form.firstname_placeholder') }}"/>
            @if ($errors->has('first_name'))
                <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('first_name') }}
                        </span>
            @endif
        </div>

        <div class="form-group {{ $errors->has('lastname') ? ' has-error ' : '' }}">
            <label for="email">
                {{ trans('texts.user.form.lastname_label') }}
            </label>
            <input type="text" name="last_name" id="lastname" value=""
                   placeholder="{{ trans('texts.user.form.lastname_placeholder') }}"/>
            @if ($errors->has('last_name'))
                <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('last_name') }}
                        </span>
            @endif
        </div>
        <div class="form-group {{ $errors->has('email') ? ' has-error ' : '' }}">
            <label for="email">
                {{ trans('texts.user.form.email_label') }}
            </label>
            <input type="text" name="email" id="email" value=""
                   placeholder="{{ trans('texts.user.form.email_placeholder') }}"/>
            @if ($errors->has('email'))
                <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('email') }}
                        </span>
            @endif
        </div>

        <div class="form-group {{ $errors->has('password') ? ' has-error ' : '' }}">
            <label for="password">
                {{ trans('texts.user.form.password_label') }}
            </label>
            <input type="password" name="password" id="password" value=""
                   placeholder="{{ trans('texts.user.form.password_placeholder') }}"/>
            @if ($errors->has('password'))
                <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('password') }}
                        </span>
            @endif
        </div>

        <div class="form-group {{ $errors->has('support_email') ? ' has-error ' : '' }}">
            <label for="password">
                {{ trans('texts.user.form.support_email_label') }}
            </label>
            <input type="text" name="support_email" id="support_email" value=""
                   placeholder="{{ trans('texts.user.form.support_email_placeholder') }}"/>
            @if ($errors->has('support_email'))
                <span class="error-block">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ $errors->first('support_email') }}
                        </span>
            @endif
        </div>


        <div class="buttons">
            <button class="button" type="submit">
                {{ trans('texts.user.form.buttons.save') }}
                <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
            </button>
        </div>
    </form>

@endsection
