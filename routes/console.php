<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

//Artisan::command('inspire', function () {
//    $this->comment(Inspiring::quote());
//})->purpose('Display an inspiring quote')->hourly();

// 每日凌晨1点执行文件清理
Schedule::command('cleanup:files')->dailyAt('01:00')->appendOutputTo(storage_path('logs/cleanup.log'));
