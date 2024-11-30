<?php

namespace App\Classes;

class Contact {


    function __construct(public string $email, public string $subject, public string $message) {
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
      }

    function getKeys(): array{
        return ["email", "subject", "message"];
    }

    function setEmail(string $email): self {
        $this->email = $email;
        return $this;
    }

    function getEmail(): string {
        return $this->email;
    }

    function setSubject(string $subject): self {
        $this->subject = $subject;
        return $this;
    }

    function getSubject(): string {
        return $this->subject;
    }

    function setMessage(string $message): self {
        $this->message = $message;
        return $this;
    }

    function getMessage(): string {
        return $this->message;
    }
}