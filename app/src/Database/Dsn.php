<?php

namespace App\Database;

class Dsn {
    private string $host;
    private string $dbname;
    private string $user;
    private string $password;
    private string $port;

    public function __construct() {
        $config = $this->getConfig();
        $this->host = $config['host'];
        $this->dbname = $config['database'];
        $this->user = $config['user'];
        $this->password = $config['password'];
        $this->port = $config['port'];
    }

    public function setHost(string $host): self {
        $this->host = $host;
        return $this;
    }

    public function getHost(): string {
        return $this->host;
    }

    public function setDbname(string $dbname): self {
        $this->dbname = $dbname;
        return $this;
    }

    public function getDbName(): string {
        return $this->dbname;
    }

    public function setUser(string $user): self {
        $this->user = $user;
        return $this;
    }

    public function setPassword(string $password): self {
        $this->password = $password;
        return $this;
    }

    public function setPort(string $port): self {
        $this->port = $port;
        return $this;
    }

    public function getPort(): string {
        return $this->port;
    }

    private function getConfig(): array {
        $file = file_get_contents(__DIR__ . '/../../config/database.json');
        return json_decode($file, true);
    }

    public function getDsn(): string {
        return "mysql:host={$this->host};dbname={$this->dbname};port={$this->port}";
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function getUser(): string {
        return $this->user;
    }
}