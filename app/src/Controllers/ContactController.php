<?php

namespace App\Controllers;

use App\Lib\Controllers\AbstractController;
use App\Lib\Http\Request;
use App\Lib\Http\Response;

class ContactController extends AbstractController
{
    private string $folder;

    public function __construct()
    {
        $this->folder = __DIR__ . '/../../var/contacts';
        if (!file_exists($this->folder)) {
            mkdir($this->folder, 0777, true);
        }
    }

    public function process(Request $request): Response
    {
        $method = $request->getMethod();

        $routeParams = [];
        if (method_exists($request, 'getRouteParams')) {
            $routeParams = $request->getRouteParams();
        }

        switch ($method) {
            case 'POST':
                return $this->handleCreate($request);
            case 'GET':
                if (!empty($routeParams) && isset($routeParams['filename'])) {
                    return $this->handleGetOne($routeParams['filename']);
                }
                return $this->handleList();
            case 'PATCH':
                if (!empty($routeParams) && isset($routeParams['filename'])) {
                    return $this->handleUpdate($routeParams['filename'], $request);
                }
                return new Response(json_encode(['error' => 'Filename missing']), 400, ['Content-Type' => 'application/json']);
            case 'DELETE':
                if (!empty($routeParams) && isset($routeParams['filename'])) {
                    return $this->handleDelete($routeParams['filename']);
                }
                return new Response(json_encode(['error' => 'Filename missing']), 400, ['Content-Type' => 'application/json']);
            default:
                return new Response(json_encode(['error' => 'Method not allowed']), 405, ['Content-Type' => 'application/json']);
        }
    }

    private function isJsonContent(Request $request): bool
    {
        $headers = $request->getHeaders();
        foreach ($headers as $k => $v) {
            if (strtolower($k) === 'content-type' || strtolower($k) === 'content_type') {
                return strpos(strtolower($v), 'application/json') !== false;
            }
        }
        return false;
    }

    private function handleCreate(Request $request): Response
    {
        if (!$this->isJsonContent($request)) {
            return new Response(json_encode(['error' => 'Content-Type must be application/json']), 400, ['Content-Type' => 'application/json']);
        }

        $data = json_decode($request->getBody(), true);
        if (!is_array($data)) {
            return new Response(json_encode(['error' => 'Invalid JSON']), 400, ['Content-Type' => 'application/json']);
        }

        $allowed = ['email', 'subject', 'message'];
        $extra = array_diff(array_keys($data), $allowed);
        if (!empty($extra) || !isset($data['email'], $data['subject'], $data['message'])) {
            return new Response(json_encode(['error' => 'Champs manquants ou propriétés non autorisées']), 400, ['Content-Type' => 'application/json']);
        }

        $timestamp = time();
        $data['dateOfCreation'] = $timestamp;
        $data['dateOfLastUpdate'] = $timestamp;

        $safeEmail = preg_replace('/[^A-Za-z0-9@._+-]/', '_', $data['email']);
        $filename = $timestamp . '_' . $safeEmail . '.json';
        file_put_contents($this->folder . '/' . $filename, json_encode($data, JSON_PRETTY_PRINT));

        $formattedDate = date('Y-m-d_H-i-s', $timestamp) . '_' . $safeEmail . '.json';

        return new Response(json_encode(['file' => $formattedDate]), 201, ['Content-Type' => 'application/json']);
    }

    private function handleList(): Response
    {
        $files = glob($this->folder . '/*.json');
        $result = [];
        foreach ($files as $f) {
            $content = json_decode(file_get_contents($f), true);
            if (is_array($content)) $result[] = $content;
        }

        return new Response(json_encode($result), 200, ['Content-Type' => 'application/json']);
    }

    private function handleGetOne(string $filename): Response
    {
        $filename = basename($filename);
        $actualFilename = $this->findFileByFormattedName($filename);
        
        if ($actualFilename === null) {
            return new Response(json_encode(['error' => 'Not found']), 404, ['Content-Type' => 'application/json']);
        }

        $content = file_get_contents($this->folder . '/' . $actualFilename);
        return new Response($content, 200, ['Content-Type' => 'application/json']);
    }
    
    private function findFileByFormattedName(string $formatted): ?string
    {
        if (file_exists($this->folder . '/' . $formatted)) {
            return $formatted;
        }
        
        $files = glob($this->folder . '/*.json');
        foreach ($files as $f) {
            $basename = basename($f);
            if ($this->formatFilename($basename) === $formatted) {
                return $basename;
            }
        }
        return null;
    }
    
    private function formatFilename(string $raw): string
    {
        preg_match('/^(\d+)_(.+)\.json$/', $raw, $matches);
        if (isset($matches[1]) && isset($matches[2])) {
            $timestamp = (int)$matches[1];
            $email = $matches[2];
            return date('Y-m-d_H-i-s', $timestamp) . '_' . $email . '.json';
        }
        return $raw;
    }

    private function handleUpdate(string $filename, Request $request): Response
    {
        if (!$this->isJsonContent($request)) {
            return new Response(json_encode(['error' => 'Content-Type must be application/json']), 400, ['Content-Type' => 'application/json']);
        }

        $filename = basename($filename);
        $actualFilename = $this->findFileByFormattedName($filename);
        
        if ($actualFilename === null) {
            return new Response(json_encode(['error' => 'Not found']), 404, ['Content-Type' => 'application/json']);
        }

        $path = $this->folder . '/' . $actualFilename;
        $data = json_decode($request->getBody(), true);
        if (!is_array($data)) {
            return new Response(json_encode(['error' => 'Invalid JSON']), 400, ['Content-Type' => 'application/json']);
        }

        $allowed = ['email', 'subject', 'message'];
        $extra = array_diff(array_keys($data), $allowed);
        if (!empty($extra)) {
            return new Response(json_encode(['error' => 'Propriétés non autorisées']), 400, ['Content-Type' => 'application/json']);
        }

        $existing = json_decode(file_get_contents($path), true);
        foreach ($data as $k => $v) {
            $existing[$k] = $v;
        }
        $existing['dateOfLastUpdate'] = time();

        file_put_contents($path, json_encode($existing, JSON_PRETTY_PRINT));

        return new Response(json_encode($existing), 200, ['Content-Type' => 'application/json']);
    }

    private function handleDelete(string $filename): Response
    {
        $filename = basename($filename);
        $actualFilename = $this->findFileByFormattedName($filename);
        
        if ($actualFilename === null) {
            return new Response(json_encode(['error' => 'Not found']), 404, ['Content-Type' => 'application/json']);
        }

        unlink($this->folder . '/' . $actualFilename);
        return new Response('', 204, []);
    }
}
