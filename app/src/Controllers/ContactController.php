<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use DateTime;

class ContactController extends AbstractController {

    public function process(Request $request): Response {
        if ($request->getMethod() === 'POST') {
            return $this->handlePost($request);
        } elseif ($request->getMethod() === 'GET') {
            $path = $request->getPath();
            if (preg_match('/^\/contact\/(.+)$/', $path, $matches)) {
                $email = $matches[1];
                return $this->handleGetSpecific($email);
            }
            return $this->handleGet();
        } elseif ($request->getMethod() === 'PATCH') {
            $path = $request->getPath();
            if (preg_match('/^\/contact\/(.+)$/', $path, $matches)) {
                $email = $matches[1];
                return $this->handlePatch($request, $email);
            }
        } elseif ($request->getMethod() === 'DELETE') {
            $path = $request->getPath();
            if (preg_match('/^\/contact\/(.+)$/', $path, $matches)) {
                $email = $matches[1];
                return $this->handleDelete($email);
            }
        }

        return new Response(json_encode(['error' => 'Method not allowed']), 405, ['Content-Type' => 'application/json']);
    }

    private function handlePost(Request $request): Response {
        // check if the request content type is application/json
        $headers = $request->getHeaders();
        if ($headers['Content-Type'] !== 'application/json') {
            return new Response(json_encode(['error' => 'Invalid Content-Type']), 400, ['Content-Type' => 'application/json']);
        }

        // set the data to check in the request body
        $data = json_decode($request->getBody(), true);

        // check request body
        if (!isset($data['email'], $data['subject'], $data['message']) || count($data) !== 3) {
            return new Response(json_encode(['error' => 'Invalid request body']), 400, ['Content-Type' => 'application/json']);
        }

        // set the contact file content and name
        $timestamp = (new DateTime())->getTimestamp();
        $filename = "{$timestamp}_{$data['email']}.json";
        $fileContent = [
            'email' => $data['email'],
            'subject' => $data['subject'],
            'message' => $data['message'],
            'dateOfCreation' => $timestamp,
            'dateOfLastUpdate' => $timestamp
        ];

        $directory = __DIR__ . "/../../var/contacts";

        // ensure the directory exists
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        // save the contact to a dedicated file
        file_put_contents("{$directory}/{$filename}", json_encode($fileContent));

        // send the response
        return new Response(json_encode(['file' => $filename]), 201, ['Content-Type' => 'application/json']);
    }

    private function handleGet(): Response {
        $directory = __DIR__ . "/../../var/contacts";
        $files = glob("{$directory}/*.json");
        $contacts = [];

        foreach ($files as $file) {
            $content = json_decode(file_get_contents($file), true);
            $contacts[] = $content;
        }

        return new Response(json_encode($contacts), 200, ['Content-Type' => 'application/json']);
    }

    private function handleGetSpecific(string $email): Response {
        $directory = __DIR__ . "/../../var/contacts";
        $files = glob("{$directory}/*_{$email}.json");

        if (empty($files)) {
            return new Response(json_encode(['error' => 'Contact form not found']), 404, ['Content-Type' => 'application/json']);
        }

        $content = json_decode(file_get_contents($files[0]), true);
        return new Response(json_encode($content), 200, ['Content-Type' => 'application/json']);
    }

    private function handlePatch(Request $request, string $email): Response {
        $directory = __DIR__ . "/../../var/contacts";
        $files = glob("{$directory}/*_{$email}.json");

        if (empty($files)) {
            return new Response(json_encode(['error' => 'Contact form not found']), 404, ['Content-Type' => 'application/json']);
        }

        $filePath = $files[0];
        $content = json_decode(file_get_contents($filePath), true);

        // check if the request content type is application/json
        $headers = $request->getHeaders();
        if ($headers['Content-Type'] !== 'application/json') {
            return new Response(json_encode(['error' => 'Invalid Content-Type']), 400, ['Content-Type' => 'application/json']);
        }

        // set the data to check in the request body
        $data = json_decode($request->getBody(), true);

        // validate request body
        $allowedKeys = ['email', 'subject', 'message'];
        foreach ($data as $key => $value) {
            if (!in_array($key, $allowedKeys)) {
                return new Response(json_encode(['error' => 'Invalid request body']), 400, ['Content-Type' => 'application/json']);
            }
        }

        // update the contact file content
        foreach ($data as $key => $value) {
            $content[$key] = $value;
        }
        $content['dateOfLastUpdate'] = (new DateTime())->getTimestamp();

        // save the updated contact to the file
        file_put_contents($filePath, json_encode($content));

        // send the response
        return new Response(json_encode($content), 200, ['Content-Type' => 'application/json']);
    }

    private function handleDelete(string $email): Response {
        $directory = __DIR__ . "/../../var/contacts";
        $files = glob("{$directory}/*_{$email}.json");

        if (empty($files)) {
            return new Response(json_encode(['error' => 'Contact form not found']), 404, ['Content-Type' => 'application/json']);
        }

        $filePath = $files[0];
        unlink($filePath);

        return new Response('', 204, ['Content-Type' => 'application/json']);
    }
}