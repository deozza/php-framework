<?php

namespace App\Entities;

class Contact {
    private string $email;
    private string $subject;
    private string $message;
    private int $dateOfCreation;
    private int $dateOfLastUpdate;

    public function __construct(string $email, string $subject, string $message, int $dateOfCreation, int $dateOfLastUpdate) {
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
        $this->dateOfCreation = $dateOfCreation;
        $this->dateOfLastUpdate = $dateOfLastUpdate;
    }

    public function toArray(): array {
        return [
            "email" => $this->email,
            "subject" => $this->subject,
            "message" => $this->message,
            "dateOfCreation" => date('Y-m-d H:i:s', $this->dateOfCreation), 
            "dateOfLastUpdate" => date('Y-m-d H:i:s', $this->dateOfLastUpdate)
        ];
    }

    public function saveToFile(string $filePath): void {
        $data = $this->toArray(); 
        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
    }

    public static function fromJsonFile(string $filePath): Contact {
        $data = json_decode(file_get_contents($filePath), true);
        return new Contact(
            $data['email'],
            $data['subject'],
            $data['message'],
            strtotime($data['dateOfCreation']), 
            strtotime($data['dateOfLastUpdate'])
        );
    }
}
