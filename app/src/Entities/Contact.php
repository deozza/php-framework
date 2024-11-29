<?php

namespace App\Entities;

class Contact
{
  private string $email;
  private string $subject;
  private string $message;
  private int $dateOfCreation;
  private int $dateOfLastUpdate;

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
    $directory = __DIR__ . '/../../var/contacts';
    // Edge case: if the directory does not exist, create it , if not error will be thrown
    if (!is_dir($directory)) {
      mkdir($directory, 0777, true);
    }

    $json = json_encode($this->bodyArray());

    file_put_contents($directory . '/' . $this->fileName(), $json);
  }
}
