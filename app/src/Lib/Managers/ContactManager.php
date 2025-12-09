<?php

namespace App\Lib\Managers;

use App\Entities\Contact;

class ContactManager
{
    private string $directory;

    public function __construct()
    {
        $this->directory = rtrim(__DIR__ . '/../../../var/contacts', DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (!is_dir($this->directory)) {
            mkdir($this->directory, 0777, true);
        }
    }

    public function getAllContacts(): array
    {
        $contacts = [];
        $files = scandir($this->directory);

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'json') {
                $fullPath = $this->directory . '/' . $file;
                $data = json_decode(file_get_contents($fullPath), true);

                if ($data) {
                    $contacts[] = $data;
                }
            }
        }

        return $contacts;
    }

    public function saveContact(Contact $contact): string
    {
        $timestamp = time();
        $filename = sprintf('%s_%s.json', $timestamp, $contact->getEmail());
        $responseFilename = date('Y-m-d_H-i-s', $timestamp) . "_{$contact->getEmail()}.json";
        $filepath = $this->directory . $filename;

        $data = json_encode($contact->toArray(), JSON_PRETTY_PRINT);
        file_put_contents($filepath, $data);

        return $responseFilename;
    }

    public function getContactByFilename(string $filename): ?array
    {
        $filePath = $this->directory . '/' . $filename . '.json';

        if (!file_exists($filePath)) {
            return null;
        }

        $contactData = json_decode(file_get_contents($filePath), true);

        return $contactData;
    }
}
