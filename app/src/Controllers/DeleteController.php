<?php

namespace App\Controllers;

use App\Lib\Controllers\AbstractController;
use App\Lib\Http\Request;
use App\Lib\Http\Response;

class DeleteController extends AbstractController
{
    public function process(Request $request): Response
    {
        
        if ($request->getMethod() !== 'DELETE') {
            return new Response(
                json_encode(["error" => "Method not allowed"]),
                405,
                ["Content-Type" => "application/json"]
            );
        }

        
        $uri = $request->getUri();
        
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        

        
        $parts = explode('/contact/', $path);

        
        if (!isset($parts[1]) || $parts[1] === '') {
            return new Response(
                json_encode(["error" => "No filename provided"]),
                400,
                ["Content-Type" => "application/json"]
            );
        }

       
        $filename = $parts[1];            
        $safeFilename = basename($filename);  

      
        if (!str_ends_with($safeFilename, '.json')) {
            $safeFilename .= '.json';
        }

        
        $path = __DIR__ . "/../../var/contacts/" . $safeFilename;

       
        if (!file_exists($path)) {
            return new Response(
                json_encode(["error" => "Contact not found"]),
                404,
                ["Content-Type" => "application/json"]
            );
        }

       
        if (!unlink($path)) {
            return new Response(
                json_encode(["error" => "Unable to delete contact"]),
                500,
                ["Content-Type" => "application/json"]
            );
        }

       
        return new Response(
            '',     
            204,   
            []      
        );
    }
}
