<?php

namespace App\Entities;

class Contact {
    private string $email;
    private string $subject;
    private string $message;
    private int $dateOfCreation;
    private int $dateOfLastUpdate;

    public function __construct(string $email, string $subject, string $message, string $dateOfCreation, string $dateOfLastUpdate) {
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
        $this->dateOfCreation = (new \DateTime($dateOfCreation))->getTimestamp();
        $this->dateOfLastUpdate = (new \DateTime($dateOfLastUpdate))->getTimestamp();
    }

    public function getFilename(): string {
        return "{$this->dateOfCreation}_{$this->email}.json";
    }

    public function toArray(): array {
        return [
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'dateOfCreation' => $this->dateOfCreation,
            'dateOfLastUpdate' => $this->dateOfLastUpdate,
        ];
    }
}       