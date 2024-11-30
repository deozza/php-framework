<?php

namespace App\Model;

class Contact
{
    private string $email;
    private string $subject;
    private string $message;
    private int $dateOfCreation;
    private int $dateOfLastUpdate;

    public function __construct(string $email, string $subject, string $message, int $dateOfCreation = null, int $dateOfLastUpdate = null)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
        $this->dateOfCreation = $dateOfCreation ?? time();
        $this->dateOfLastUpdate = $dateOfLastUpdate ?? time();
    }

    public function save(string $directory): void
    {
        $filename = $this->getFilename();
        $filePath = $directory . $filename;

        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($filePath, json_encode($this->toArray(), JSON_PRETTY_PRINT));
    }

    public function getFilename(): string
    {
        return $this->dateOfCreation . '_' . $this->email . '.json';
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'dateOfCreation' => $this->dateOfCreation,
            'dateOfLastUpdate' => $this->dateOfLastUpdate,
        ];
    }

    public static function load(string $filename, string $directory): ?self
    {
        $filePath = $directory . $filename;

        if (!file_exists($filePath)) {
            return null;
        }

        $data = json_decode(file_get_contents($filePath), true);

        return new self(
            $data['email'],
            $data['subject'],
            $data['message'],
            $data['dateOfCreation'],
            $data['dateOfLastUpdate']
        );
    }
}