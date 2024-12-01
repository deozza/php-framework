<?php

namespace App;

class Contact
{
    private string $email;
    private string $subject;
    private string $message;
    private int $createdAt;
    private int $lastUpdatedAt;

    public function __construct(string $email, string $subject, string $message)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
        $this->createdAt = time();
        $this->lastUpdatedAt = $this->createdAt;
    }

    public function save(): string
    {
        $cleanedEmail = str_replace('@', '_at_', $this->email);
        $filename = $this->createdAt . '_' . $cleanedEmail . '.json';

        $data = [
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'createdAt' => $this->createdAt,
            'lastUpdatedAt' => $this->lastUpdatedAt,
        ];

        $filePath = __DIR__ . '/../var/contacts/' . $filename;
        file_put_contents($filePath, json_encode($data));

        return $filename;
    }

    public static function loadFromFile(string $filename)
    {
        $filePath = __DIR__ . '/../var/contacts/' . $filename;
    
        
        if (!file_exists($filePath)) {
            return null; 
        }
    
        
        $data = json_decode(file_get_contents($filePath), true);
    
        
        $contact = new Contact($data['email'], $data['subject'], $data['message']);
        $contact->createdAt = $data['createdAt'];
        $contact->lastUpdatedAt = $data['lastUpdatedAt'];
    
        
        return $contact;
    }
    

    public function update(array $data): void
    {
        if (isset($data['email'])) {
            $this->email = $data['email'];
        }
        if (isset($data['subject'])) {
            $this->subject = $data['subject'];
        }
        if (isset($data['message'])) {
            $this->message = $data['message'];
        }
        $this->lastUpdatedAt = time();
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'createdAt' => $this->createdAt,
            'lastUpdatedAt' => $this->lastUpdatedAt,
        ];
    }
}
