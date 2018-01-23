<?php

//ws
// Command that starts the built-in web server
$commandws = sprintf(
        'php -S %s:%d -t %s >/dev/null 2>&1 & echo $!', WEB_SERVER_HOST, WEB_SERVER_PORT, WEB_SERVER_DOCROOT
);
// Execute the command and store the process ID
$outputws = array();
exec($commandws, $outputws);
//var_dump($output);
$pidws = (int) $outputws[0];

echo sprintf(
        '%s - Web server started on %s:%d with PID %d', date('r'), WEB_SERVER_HOST, WEB_SERVER_PORT, $pidws
) . PHP_EOL;

//Selenium
// Command that starts the built-in web server
$commandse = sprintf(
        'sh %s > /dev/null 2>&1 & echo $!', __DIR__ . '/../../vendor/bin/selenium-server-standalone'
);
//echo $commandse;
// Execute the command and store the process ID
$outputse = array();
exec($commandse, $outputse);
//var_dump($output);
$pidse = (int) $outputse[0];

echo sprintf(
        'Selenium server started with PID %d', $pidse
) . PHP_EOL;


// Kill the web server when the process ends
register_shutdown_function(function() use ($pidws) {
    echo sprintf('%s - Killing process with ID %d', date('r'), $pidws) . PHP_EOL;
    exec('kill ' . $pidws);
});

// Kill the web server when the process ends
register_shutdown_function(function() use ($pidse) {
    echo sprintf('%s - Killing process with ID %d', date('r'), $pidse) . PHP_EOL;
    exec('kill ' . $pidse);
});

