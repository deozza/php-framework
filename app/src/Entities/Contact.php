<?php

namespace App\Entities;

class Contact
{
    public string $email;
    public string $subject;
    public string $message;
    public int $dateOfCreation;
    public int $dateOfLastUpdate;

    public function __construct(string $email, string $subject, string $message)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
        $this->dateOfCreation = time();
        $this->dateOfLastUpdate = time();
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'DateOfCreation' => $this->dateOfCreation,
            'DateOfLastUpdate' => $this->dateOfLastUpdate,
        ];
    }

    public function saveToFile(string $filepath): void
    {
        $data = json_encode($this->toArray(), JSON_PRETTY_PRINT);
        file_put_contents($filepath, $data);
    }


    public function getEmail(): string
    {
        return $this->email;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getDateOfCreation(): int
    {
        return $this->dateOfCreation;
    }

    public function getDateOfLastUpdate(): int
    {
        return $this->dateOfLastUpdate;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function setDateOfLastUpdate(int $timestamp): void
    {
        $this->dateOfLastUpdate = $timestamp;
    }

    public function setDateOfCreation(int $timestamp): void
    {
        $this->dateOfCreation = $timestamp;
    }
}
