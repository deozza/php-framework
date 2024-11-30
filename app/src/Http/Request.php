<?php

namespace App\Http;

use App\Classes\Contact;

class Request {
    private string $uri;
    private string $method;
    private array $headers;
    private array $body;
    private Contact $contact;
    private array $data;

    public function __construct() {
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->headers = getallheaders();
        $this->body = json_decode(file_get_contents('php://input'), true) ?? [];
        $this->contact = new Contact('zboui','zboui','zboui');
    }

    public function getUri(): string {
        return $this->uri;
    }

    public function getMethod(): string {
        return $this->method;
    }

    public function getHeaders(): array {
        return $this->headers;
    }

    public function getBody(): array {
        return $this->body;
    }

    public function checkValidity(): Response {
        //recuperer l'email de la requete
        $body = $this->getBody();
        $email = $body["email"] ?? null;
        $domain = stristr($email, '@');
        $extension = stristr($domain, '.');
        $position = stripos($email, $extension);
        $sub = substr($email, 0, $position);
    
        // Chemin d'accès au dossier et nom du fichier
        $path = __DIR__ . '/../../var/contact/';
        $filename = $path . (string)time() . $sub . ".json";
    
        // Compare les clés 
        $requiredKeys = $this->contact->getKeys();
        $keysFromRequest = array_keys($body);
    
        if ($requiredKeys !== $keysFromRequest) {
            return new Response('Mauvais format. La requête doit avoir un email, un sujet et un message',400,$this->getHeaders());
        }
    
        // Récupérer la liste des fichiers dans le dossier
        $files = array_filter(scandir($path), function ($item) {
            return $item !== '.' && $item !== '..';
        });
    
        //envoyer dans le fichier, le body en formé comme un json
        if (count($files) === 0) {
            if (file_put_contents($filename, json_encode([$body], JSON_PRETTY_PRINT))) {
                return new Response('Données enregistrées dans un nouveau fichier.',200,$this->getHeaders());
            } else {
                return new Response('Erreur lors de l\'enregistrement des données.',500,$this->getHeaders());
            }
        }
    
        // Récuperer le nom du fichier
        foreach ($files as $file) {
            $filePath = $path . $file;
    
            if (is_file($filePath)) {
                $data = json_decode(file_get_contents($filePath), true);
    
                // Ajouter les nouvelles données
                $data[] = $body;
    
                // Mettre à jour le fichier
                if (file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT))) {
                    $newName = $path . (string)time() . $sub . ".json";

                    if (rename($filePath, $newName)) {
                        return new Response(
                            'Données ajoutées et fichier renommé.',
                            200,
                            $this->getHeaders()
                        );}

                    return new Response('Données ajoutées au fichier existant.',200,$this->getHeaders());
                } else {
                    return new Response('Erreur lors de l\'ajout des données au fichier existant.',500,$this->getHeaders());
                }
            }
        }
        return new Response('Mauvais format. La requête doit avoir un email, un sujet et un message',400,$this->getHeaders());
    }
    

    public function fetchAll(): string{
        // $path = __DIR__ . '/../../var/contact/';
        // $files = scandir($path);
        //     foreach($files as $file) {
        //         //do your work here
        //         var_dump(is_file($file)) . "\n";
        //     }
        // var_dump($files);
        // file_get_contents('php://input')
        return "";
    }
}