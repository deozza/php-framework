<?php

namespace App\Database;

class DatabaseConnexion
{
    private \PDO $connexion;

    public function setConnexion(): self
    {
        $dsn = new Dsn();
        $this->connexion = new \PDO($dsn->getDsn(), $dsn->getUser(), $dsn->getPassword());
        return $this;
    }

    public function getConnexion(): \PDO
    {
        return $this->connexion;
    }

    public function deleteConnexion(): void
    {
        $this->connexion = null;
    }

}