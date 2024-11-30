<?php

require_once __DIR__ . '/../vendor/autoload.php';

$options = getopt('c:');

if (!isset($options['c'])) {
    echo 'You must provide a command to execute' . PHP_EOL;
    exit(1);
}

$command = $options['c'];

$commandClassName = 'App\\Commands\\' . ucfirst($command);

if (!class_exists($commandClassName)) {
    echo 'Command not found' . PHP_EOL;
    exit(1);
}

$commandInstance = new $commandClassName();

if(!is_subclass_of($commandInstance, 'App\Commands\AbstractCommand')) {
    echo 'Command not found' . PHP_EOL;
    exit(1);
} 

$commandInstance->execute();
exit(0);