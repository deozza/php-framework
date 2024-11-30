<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Entities\Contact;
use App\Controllers\AbstractController;


class GetAllContactController extends AbstractController
{
  public function process(Request $request): Response
  { 
    $contacts = Contact::findAll(); 
    return $this->jsonResponse($contacts,200); 
  }

}