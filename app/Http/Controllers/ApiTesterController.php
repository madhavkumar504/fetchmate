<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiTesterController extends Controller
{
    public function index()
    {
        return view('api-tester');
    }

    public function sendRequest(Request $request)
    {
        $method = strtoupper($request->input('method'));
        $url = $request->input('url');
        $headers = $request->input('headers', []);
        $body = $request->input('body', []);

        $client = new Client();

        try {
            $options = [
                'headers' => $headers,
                'http_errors' => false // prevent Guzzle from throwing on 4xx/5xx
            ];

            if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
                $options['json'] = $body;
            }

            $response = $client->request($method, $url, $options);

            return response()->json([
                'status' => $response->getStatusCode(),
                'headers' => $response->getHeaders(),
                'body' => json_decode($response->getBody(), true) ?? (string) $response->getBody()
            ]);
        } catch (RequestException $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'response' => $e->hasResponse() ? (string) $e->getResponse()->getBody() : null
            ], 500);
        }
    }
}
