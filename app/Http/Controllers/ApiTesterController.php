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
        $body = $request->input('body');

        $client = new Client([
            'verify' => false, // Disable SSL verification (for local/testing)
            'http_errors' => false // Do not throw exceptions for 4xx/5xx
        ]);

        $options = [
            'headers' => $headers,
        ];

        // Determine Content-Type
        $contentType = $headers['Content-Type'] ?? $headers['content-type'] ?? null;

        // Handle body based on content type
        if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
            if ($contentType === 'application/json') {
                $options['body'] = is_array($body) ? json_encode($body) : $body;
            } elseif ($contentType === 'application/x-www-form-urlencoded') {
                $options['form_params'] = $body;
            } elseif ($contentType && str_contains($contentType, 'multipart/form-data')) {
                // Convert to multipart array
                $multipart = [];
                foreach ($body as $key => $value) {
                    $multipart[] = [
                        'name' => $key,
                        'contents' => $value
                    ];
                }
                $options['multipart'] = $multipart;
            } else {
                $options['body'] = is_array($body) ? http_build_query($body) : $body;
            }
        }

        try {
            $response = $client->request($method, $url, $options);

            $rawBody = (string) $response->getBody();
            $parsedBody = json_decode($rawBody, true);

            return response()->json([
                'status' => $response->getStatusCode(),
                'headers' => $response->getHeaders(),
                'body' => $parsedBody ?? $rawBody // send raw if not JSON
            ]);
        } catch (RequestException $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'response' => $e->hasResponse() ? (string) $e->getResponse()->getBody() : null
            ], 500);
        }
    }
}
