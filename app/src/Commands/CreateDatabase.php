<?php


namespace App\Commands;

use App\Database\DatabaseConnexion;
use App\Database\Dsn;


class CreateDatabase extends AbstractCommand {
    
    public function execute(): void
    {
        $db = new DatabaseConnexion();
        $dsn = new Dsn();
        $dsn->addHostToDsn()
            ->addPortToDsn();
        $db->setConnexion($dsn);
        $db->getConnexion()->exec("CREATE DATABASE IF NOT EXISTS {$dsn->getDbName()};");
    }

    public function undo(): void
    {
    }

    public function redo(): void
    {
    }
    
}

?>
