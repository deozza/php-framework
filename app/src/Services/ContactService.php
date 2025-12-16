<?php


namespace App\Services;

use App\Entities\Contact;

class ContactService {
    
    const CONTACT_FILENAME_FORMAT = '%d_%s.json';
    const CONTACT_DIR = __DIR__ . '/../../var/contacts/';
    
    private function buildContact(array $payload): Contact {

        $contact = new Contact();
        
        $contact->email = $payload['email'];
        $contact->subject = $payload['subject'];
        $contact->message = $payload['message'];

        return $contact;
    }
    
    private function saveContact(Contact $contact): void {
        $filename = sprintf(self::CONTACT_FILENAME_FORMAT, $contact->getDateOfCreation(), $contact->email);

        file_put_contents(self::CONTACT_DIR . $filename, json_encode($contact, JSON_PRETTY_PRINT));
    }
    
}

?>
