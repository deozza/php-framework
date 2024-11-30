<?php

namespace App\Entities;

class Contact
{
  private string $email;
  private string $subject;
  private string $message;
  private int $dateOfCreation;
  private int $dateOfLastUpdate;
  private string $directory = __DIR__ . '/../../var/contacts';

  public function __construct(string $email, string $subject, string $message)
  {
    $this->email = $email;
    $this->subject = $subject;
    $this->message = $message;
    $this->dateOfCreation = time();
    $this->dateOfLastUpdate = time();
  }

  public function bodyArray(): array
  {
    return [
      'email' => $this->email,
      'subject' => $this->subject,
      'message' => $this->message,
      'dateOfCreation' => $this->dateOfCreation,
      'dateOfLastUpdate' => $this->dateOfLastUpdate
    ];
  }

  public function fileName(): string
  {
    return $this->dateOfCreation . '_' . $this->email . '.json';
  }

  public function save()
  {
    $directory = $this->directory;
    // Edge case: if the directory does not exist, create it , if not error will be thrown
    if (!is_dir($directory)) {
      mkdir($directory, 0777, true);
    }

    $json = json_encode($this->bodyArray());

    file_put_contents($directory . '/' . $this->fileName(), $json);
  }

  public static function findAll(): array
  {
    $directory = __DIR__ . '/../../var/contacts';

    $files = glob($directory . '/*.json');

    $contacts = [];

    foreach ($files as $file) {
      $json = file_get_contents($file);
      $contacts[] = json_decode($json, true);
    }

    return $contacts;
  }

  /*
  // Useless function since we are using the filename to get the contact
  public static function findOne(string $uri): array
  {
    $directory = __DIR__ . '/../../var/contacts';
    $files = glob($directory . '/*_' . $uri . '.json');

    if (count($files) === 0) {
      return null; // No file found
    }

    $file = $files[0];
    $json = file_get_contents($file);
    // je suppose que y'a qu'un seul fichier par email

    return json_decode($json, true);
  }*/

  public static function jsonRequestReader(string $filePath): self
  {
    $data = json_decode(file_get_contents($filePath), true);
    $contact = new self($data['email'], $data['subject'], $data['message']);
    $contact->dateOfCreation = $data['dateOfCreation'];
    $contact->dateOfLastUpdate = $data['dateOfLastUpdate'];
    return $contact;
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

  public function update(array $body): void
  {
    $this->setEmail($body['email']);
    $this->setSubject($body['subject']);
    $this->setMessage($body['message']);
    $this->setDateOfLastUpdate(time());
  }

  public static function fieldsCheckValid(array $data): bool
  {
    $allowedKeys = ['email', 'subject', 'message'];
    return empty(array_diff(array_keys($data), $allowedKeys));
  }

  public function saveFile(string $filePath): void
  {
    $data = $this->bodyArray();
    file_put_contents($filePath, json_encode($data));
  }
}
