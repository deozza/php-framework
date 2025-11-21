<?php

require_once __DIR__ . '/../vendor/autoload.php';

$inputs = getopt('c:');

if(!isset($inputs['c'])) {
    echo 'You must provide a command to execute' . PHP_EOL;
    exit(1);
}

$command = $inputs['c'];
$commandClassName = 'App\\Commands\\' . $command;

if(class_exists($commandClassName) == false) {
    echo 'Command not found' . PHP_EOL;
    exit(1);
}

$commandInstance = new $commandClassName();

if(is_subclass_of($commandInstance, 'App\Commands\AbstractCommand') === false) {
    echo 'Command not found' . PHP_EOL;
    exit(1);
}


$commandInstance->execute();
exit(0);

?>
