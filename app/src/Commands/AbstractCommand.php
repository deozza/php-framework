<?php

namespace App\Commands;

abstract class AbstractCommand {
    abstract public function execute(): bool;
}