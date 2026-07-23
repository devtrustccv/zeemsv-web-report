<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ErrorResponse;

class ApiToken
{
    public function makeApiRequestToken()
    {
        $url = env('LINK_TOKEN');

        $payload = [
            'client_id' =>"dge-central-base",
            //env('CLIENTE_ID'),
            'client_secret' => "fOA5mn0XvyXcsnPQnJnweQOkWNOuQZwO"
            //env('CLIENTE_SECRECT'),
        ];

        $response = Http::withOptions([
            'verify' => false,
        ])->post($url, $payload); // Envia como JSON automaticamente

        if ($response->successful()) {
            return $response->json('access_token');
        }

        Log::error('Erro ao obter token DGE', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        abort(502,'Erro ao obter token DGE');
    }
}

