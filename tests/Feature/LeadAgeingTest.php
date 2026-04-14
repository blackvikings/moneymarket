<?php

use App\Models\Lead;
use Carbon\Carbon;

test('lead ageing is minimum 1 day and supports ageing alias', function () {
    Carbon::setTestNow(Carbon::parse('2026-04-14 10:00:00'));

    $lead = new Lead();
    $lead->converted_at = Carbon::parse('2026-04-14 09:00:00');

    expect($lead->aging)->toBe('1 day');
    expect($lead->ageing)->toBe('1 day');

    Carbon::setTestNow();
});

