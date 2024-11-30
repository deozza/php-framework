<?php

namespace App\Entities;

class Contact
{
    private string $email;
    private string $subject;
    private string $message;
    private int $dateOfCreation;
    private int $dateOfLastUpdate;

    public function __construct($email, $subject, $message, $dateOfCreation = null, $dateOfLastUpdate = null)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
        $this->dateOfCreation = $dateOfCreation ?? time();
        $this->dateOfLastUpdate = $dateOfLastUpdate ?? time();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getDateOfCreation(): int
    {
        return $this->dateOfCreation;
    }

    public function getDateOfLastUpdate(): int
    {
        return $this->dateOfLastUpdate;
    }

    public function setDateOfLastUpdate(): void
    {
        $this->dateOfLastUpdate = time();
    }
}
