<?php

use WebmanTech\LaravelProcess\Facades\Process;

test('run', function () {
    $result = Process::run('ls');

    expect($result->output())->toBeString()
        ->and($result->successful())->toBeTrue();
});
