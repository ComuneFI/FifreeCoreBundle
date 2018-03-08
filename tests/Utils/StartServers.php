<?php

//ws
// Command that starts the built-in web server
$commandws = sprintf(
        'php %s %s > %s 2>&1 & echo $!', 'bin/console server:start', '--docroot=tests/public', __DIR__ . '/../../build/artifacts/logs/webserver.log'
);

echo $commandws . "\n";

// Execute the command and store the process ID
$outputws = array();
exec($commandws, $outputws);
//var_dump($output);
$pidws = (int) $outputws[0];

echo sprintf(
        '%s - Web server started with PID %d', date('r'), $pidws
) . PHP_EOL;

//Selenium
// Command that starts the built-in web server
$driverspath = realpath(__DIR__ . '/../../vendor') . "/bin/";
$commandse = sprintf(
        'PATH=$PATH:%s && sh %s %s > %s 2>&1 & echo $!', $driverspath, __DIR__ . '/../../vendor/bin/selenium-server-standalone', '-enablePassThrough false', __DIR__ . '/../../build/artifacts/logs/selenium2.log'
);

echo $commandse . "\n";

// Execute the command and store the process ID
$outputse = array();
exec($commandse, $outputse);
//var_dump($output);
$pidse = (int) $outputse[0];

echo sprintf(
        'Selenium server started with PID %d', $pidse
) . PHP_EOL;

sleep(3);

// Kill the web server when the process ends
register_shutdown_function(function() use ($pidws) {
// Command that starts the built-in web server
    $commandws = sprintf(
            'php %s %s >> %s 2>&1 & echo $!', 'bin/console server:stop', '--env=test', __DIR__ . '/../../build/artifacts/logs/webserver.log'
    );

    echo $commandws . "\n";

// Execute the command and store the process ID
    $outputws = array();
    exec($commandws, $outputws);
//var_dump($output);
    $pidwstop = (int) $outputws[0];

    echo sprintf(
            '%s - Web server stopped with PID %d -> %d', date('r'), $pidws, $pidwstop
    ) . PHP_EOL;
    //echo sprintf('%s - Killing process with ID %d', date('r'), $pidws) . PHP_EOL;
    //exec('kill ' . $pidws);
    sleep(3);
});

// Kill the web server when the process ends
register_shutdown_function(function() use ($pidse) {
    echo sprintf('%s - Killing process with ID %d', date('r'), $pidse) . PHP_EOL;
    exec('kill ' . $pidse);
    exec('killall geckodriver');
    exec('killall chromedriver');
    sleep(3);
});
