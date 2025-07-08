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
        $url = $request->input('url');
        $method = strtolower($request->input('method', 'get'));
        $headers = $request->input('headers', []);
        $body = $request->input('body', []);

        // Clean header keys
        $formattedHeaders = [];
        foreach ($headers as $key => $value) {
            if (!empty($key)) {
                $formattedHeaders[$key] = $value;
            }
        }

        $response = Http::withHeaders($formattedHeaders)->$method($url, $body);

        return response()->json([
            'status' => true,
            'statuscode' => $response->status(),
            'body' => $response->json(), // or use ->body() if not JSON
        ]);
    }
}
