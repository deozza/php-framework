<?php

namespace App\Entities;

class Artist extends AbstractEntity {
    public int $id;
    public string $name;

    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

}