<?php

use Illuminate\Support\Facades\Log;

test('testLogging', function () {
    Log::info("Hello Info");
    Log::warning("Hello warning");
    Log::error("Hello error");
    Log::critical("Hello critical");

    expect(true)->toBeTrue();
});
