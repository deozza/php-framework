<?php

namespace App\Forms;

class ContactForm {
    
    const EXPECTED_PAYLOAD_PROPERTIES = ['subject', 'message', 'email'];
    
    public function checkPayloadIsValid(array $payload): bool {
        if(count(self::EXPECTED_PAYLOAD_PROPERTIES) !== count($payload)) {
            return false;
        }

        if(empty(array_diff(self::EXPECTED_PAYLOAD_PROPERTIES, array_keys($payload))) === false) {
            return false;
        }

        return true;
    }
}

?>
