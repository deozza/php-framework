<?php

namespace App\Controllers;

use App\Lib\Controllers\AbstractController;
use App\Lib\Http\Request;
use App\Lib\Http\Response;

class ContactController extends AbstractController
{
    private string $contactsDir;

    public function __construct()
    {
        $this->contactsDir = __DIR__ . '/../../var/contacts';
        if (!is_dir($this->contactsDir)) {
            mkdir($this->contactsDir, 0777, true);
        }
    }

    public function process(Request $request): Response
    {
        $method = $request->getMethod();

        switch ($method) {
            case 'POST':
                return $this->store($request);
            case 'GET':
                $params = $request->getParams();
                if (isset($params['filename'])) {
                    return $this->show($request, $params['filename']);
                }
                
                if (isset($_GET['filename'])) {
                    return $this->show($request, $_GET['filename']);
                }
                return $this->index($request);
            case 'PATCH':
                $params = $request->getParams();
                $fn = $params['filename'] ?? ($_GET['filename'] ?? null);
                return $this->update($request, $fn);
            case 'DELETE':
                $params = $request->getParams();
                $fn = $params['filename'] ?? ($_GET['filename'] ?? null);
                return $this->destroy($request, $fn);
            default:
                return new Response(json_encode(['error' => 'Method not allowed']), 405, ['Content-Type' => 'application/json']);
        }
    }

    private function getHeader(Request $request, string $name): ?string
    {
        $headers = $request->getHeaders();
        foreach ($headers as $k => $v) {
            if (strtolower($k) === strtolower($name)) {
                return $v;
            }
        }

        return null;
    }

    private function store(Request $request): Response
    {
        $contentType = $this->getHeader($request, 'Content-Type') ?? '';
        if (stripos($contentType, 'application/json') === false) {
            return new Response(json_encode(['error' => 'Invalid content type']), 400, ['Content-Type' => 'application/json']);
        }

        $body = file_get_contents('php://input');
        $data = json_decode($body, true);

        $allowed = ['email', 'subject', 'message'];
        if (!is_array($data) || array_diff(array_keys($data), $allowed) || array_diff($allowed, array_keys($data))) {
            return new Response(json_encode(['error' => 'Invalid request body']), 400, ['Content-Type' => 'application/json']);
        }

        $timestamp = time();
        $filename = $timestamp . '_' . $data['email'] . '.json';
        $filepath = $this->contactsDir . '/' . $filename;

        $content = [
            'email' => $data['email'],
            'subject' => $data['subject'],
            'message' => $data['message'],
            'dateOfCreation' => $timestamp,
            'dateOfLastUpdate' => $timestamp
        ];

        file_put_contents($filepath, json_encode($content));

        $formattedName = date('Y-m-d_H-i-s', $timestamp) . '_' . $data['email'] . '.json';

        return new Response(json_encode(['file' => $formattedName]), 201, ['Content-Type' => 'application/json']);
    }

    private function index(Request $request): Response
    {
        $files = glob($this->contactsDir . '/*.json');
        $contacts = [];

        foreach ($files as $file) {
            $contacts[] = json_decode(file_get_contents($file), true);
        }

        return new Response(json_encode($contacts), 200, ['Content-Type' => 'application/json']);
    }

    private function show(Request $request, ?string $filename): Response
    {
        if (!$filename) {
            return new Response(json_encode(['error' => 'Filename required']), 400, ['Content-Type' => 'application/json']);
        }

        $filename = basename($filename);
        $filepath = $this->contactsDir . '/' . $filename;

        if (!file_exists($filepath)) {
            $email = null;
            if (preg_match('/[A-Za-z0-9._%+\-]+@[A-Za-z0-9._%+\-]+/', $filename, $m)) {
                $email = $m[0];
            }

            if ($email) {
                $candidates = glob($this->contactsDir . '/*_' . $email . '.json');
                if (!empty($candidates)) {
                    $filepath = $candidates[0];
                }
            }
        }

        if (!file_exists($filepath)) {
            return new Response(json_encode(['error' => 'Contact not found']), 404, ['Content-Type' => 'application/json']);
        }

        $content = file_get_contents($filepath);
        return new Response($content, 200, ['Content-Type' => 'application/json']);
    }

    private function update(Request $request, ?string $filename): Response
    {
        if (!$filename) {
            return new Response(json_encode(['error' => 'Filename required']), 400, ['Content-Type' => 'application/json']);
        }

        $filename = basename($filename);
        $filepath = $this->contactsDir . '/' . $filename;

        if (!file_exists($filepath)) {
            $email = null;
            if (preg_match('/[A-Za-z0-9._%+\-]+@[A-Za-z0-9._%+\-]+/', $filename, $m)) {
                $email = $m[0];
            }

            if ($email) {
                $candidates = glob($this->contactsDir . '/*_' . $email . '.json');
                if (!empty($candidates)) {
                    $filepath = $candidates[0];
                }
            }
        }

        if (!file_exists($filepath)) {
            return new Response(json_encode(['error' => 'Contact not found']), 404, ['Content-Type' => 'application/json']);
        }

        $body = file_get_contents('php://input');
        $data = json_decode($body, true);
        $allowed = ['email', 'subject', 'message'];

        if (!is_array($data) || array_diff(array_keys($data), $allowed)) {
            return new Response(json_encode(['error' => 'Invalid request body']), 400, ['Content-Type' => 'application/json']);
        }

        $contact = json_decode(file_get_contents($filepath), true);
        foreach ($data as $key => $value) {
            $contact[$key] = $value;
        }
        $contact['dateOfLastUpdate'] = time();

        file_put_contents($filepath, json_encode($contact));

        return new Response(json_encode($contact), 200, ['Content-Type' => 'application/json']);
    }

    private function destroy(Request $request, ?string $filename): Response
    {
        if (!$filename) {
            return new Response(json_encode(['error' => 'Filename required']), 400, ['Content-Type' => 'application/json']);
        }

        $filename = basename($filename);
        $filepath = $this->contactsDir . '/' . $filename;

        if (!file_exists($filepath)) {
            $email = null;
            if (preg_match('/[A-Za-z0-9._%+\-]+@[A-Za-z0-9._%+\-]+/', $filename, $m)) {
                $email = $m[0];
            }

            if ($email) {
                $candidates = glob($this->contactsDir . '/*_' . $email . '.json');
                if (!empty($candidates)) {
                    $filepath = $candidates[0];
                }
            }
        }

        if (!file_exists($filepath)) {
            return new Response(json_encode(['error' => 'Contact not found']), 404, ['Content-Type' => 'application/json']);
        }

        unlink($filepath);

        return new Response('', 204, []);
    }
}
