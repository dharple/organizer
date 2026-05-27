<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('data:dump')
    ->hourly()
    ->appendOutputTo(storage_path('logs/data-dump.log'));
