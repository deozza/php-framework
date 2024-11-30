<?php

namespace App\Database;

class Dsn
{
    private string $host;
    private string $database;
    private string $user;
    private string $password;
    private string $port;

    public function __construct()
    {
        $config = $this->getConfig();
        $this->host = $config['host'];
        $this->database = $config['database'];
        $this->user = $config['user'];
        $this->password = $config['password'];
        $this->port = $config['port'];
    }

    public function getConfig(): array
    {
        $file = file_get_contents(__DIR__ . '/../../config/database.json');
        return json_decode($file, true);
    }

    public function getDsn(): string
    {
        return "mysql:host={$this->host};dbname={$this->database};port={$this->port}";
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getPort(): string
    {
        return $this->port;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getDbName(): string
    {
        return $this->database;
    }
}
