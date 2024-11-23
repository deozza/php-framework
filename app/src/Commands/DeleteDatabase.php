<?php

namespace App\Commands;

use App\Database\Dsn;

class DeleteDatabase extends AbstractCommand {
    
    public function execute(): bool {
        try {
            $dsn = new Dsn();
            $db = new \PDO("mysql:host={$dsn->getHost()};port={$dsn->getPort()}", $dsn->getUser(), $dsn->getPassword());

            $db->exec("DROP DATABASE IF EXISTS {$dsn->getDbName()};");

            $db = null;
            return true;
        } catch (\PDOException $e) {
            echo $e->getMessage();
            
            $db = null;
            return false;
        }
    }
}