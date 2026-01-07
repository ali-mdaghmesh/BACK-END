<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\DB;

Artisan::command('reservations:update-ended', function () {

    $done = DB::table('reservations')
        ->where('end_date','<=', Carbon::today())
        ->where('status', 'approved')
        ->update(['status' => 'done']);

        $expired = DB::table('reservations')
        ->where('status', '!=', 'approved')           
        ->where('status', '!=', 'done')              
        ->where('status', '!=', 'expired')   
        ->where('status', '!=', 'rejected')       
        ->where('status', '!=', 'cancelled')
        ->where('start_date', '<=', Carbon::today())
        ->update(['status' => 'expired']);

    $this->info("successfully updated $done reservations to done status");
    $this->info("successfully updated $expired reservations to expired status");
});



Schedule::command('reservations:update-ended')->everyMinute();
