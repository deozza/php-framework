<?php

namespace App\Entities;

class Contact  {

    public string $email;
    public string $subject;
    public string $message;
    public int $dateOfCreation;
    public int $dateOfUpdate;

    public function __construct(?string $email = null, ?string $subject = null, ?string $message = null) {
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
    }
    
    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getSubject(): string {
        return $this->subject;
    }

    public function setSubject($subject) {
        $this->subject = $subject;
    }

    public function getMessage(): string {
        return $this->message;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function getDateOfCreation(): int {
        return $this->dateOfCreation;
    }

    public function setDateOfCreation($dateOfCreation) {
        $this->dateOfCreation = $dateOfCreation;
    }

    public function getDateOfUpdate(): int {
        return $this->dateOfUpdate;
    }

    public function setDateOfUpdate($dateOfUpdate) {
        $this->dateOfUpdate = $dateOfUpdate;
    }

}