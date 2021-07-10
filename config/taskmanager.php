<?php

return [
    'locale'                  => env('DEFAULT_LOCALE', 'en'),
    'app_name'                => 'Taskmanager',
    'app_domain'              => env('APP_DOMAIN', 'taskman.develop'),
    'key_length'              => 64,
    'app_version'             => '1.0',
    'api_version'             => '1.0',
    'support_email'           => 'support@taskmanager.co.uk',
    'web_url'                 => 'http://taskman.develop',
    'site_url'                => env('APP_URL', ''),
    'currency_converter_key'  => env('CURRENCY_CONVERTER_KEY'),
    'from_email'              => env('FROM_EMAIL', 'support@tamtamcrm.com'),
    'from_name'               => 'Michael Hampton',
    'use_live_exchange_rates' => false,
    'stripe_api_key'          => env('STRIPE_API_KEY'),
    'stripe_client_id'        => env('STRIPE_CLIENT_ID'),
    'slow_query_log_enabled'  => env('LARAVEL_SLOW_QUERY_LOGGER_ENABLED', false),
    'channel'                 => env('LARAVEL_SLOW_QUERY_LOGGER_CHANNEL', 'single'),
    'log-level'               => env('LARAVEL_SLOW_QUERY_LOGGER_LOG_LEVEL', 'debug'),
    'time-to-log'             => env('LARAVEL_SLOW_QUERY_LOGGER_TIME_TO_LOG', 10),
    'notify_on_login'         => true,
    'disk'                    => 'public',
    'downloads_dir'           => 'downloads',
    'exports_dir'             => 'exports',
    'export_database'         => 'mike'
];