<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Schedule::call(function () {
    $url = config('app.url') . '/notifications/send';
    $context = stream_context_create(['http' => ['timeout' => 30]]);
    @file_get_contents($url, false, $context);
})->everyMinute();
