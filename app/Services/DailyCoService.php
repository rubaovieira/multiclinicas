<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class DailyCoService
{
    private $apiKey;
    private $baseUrl = 'https://api.daily.co/v1';

    public function __construct()
    {
        $this->apiKey = env('API_KEY_DAILY_CO');
    }

    public function createRoom($nbf = null, $exp = null)
    {

        $roomName = uniqid('room_');

        $data = [
            'name' => $roomName,
            'properties' => [
                'lang' => 'pt-BR'
            ]
        ];

        // Adiciona nbf se for fornecido
        if ($nbf !== null) {
            $data['properties']['nbf'] = $nbf;
        }

        // Adiciona exp se for fornecido
        if ($exp !== null) {
            $data['properties']['exp'] = $exp;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json'
        ])->post($this->baseUrl . '/rooms', $data);

        if ($response->successful()) {
            $data = $response->json();
            return [
                'success' => true,
                'room_name' => $roomName,
                'url' => $data['url'] ?? null,
                'expires_at' => $data['expires_at'] ?? null
            ];
        }

        return [
            'success' => false,
            'error' => $response->json()['error'] ?? 'Erro ao criar sala'
        ];
    }

    public function deleteRoom($roomName)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json'
        ])->delete($this->baseUrl . '/rooms/' . $roomName);

        if ($response->successful()) {
            return [
                'success' => true,
                'message' => 'Sala excluída com sucesso'
            ];
        }

        return [
            'success' => false,
            'error' => $response->json()['error'] ?? 'Erro ao excluir sala'
        ];
    }
}
