<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function current(Request $request): JsonResponse
    {
        $cityName = $request->query('city', 'Guarapuava');

        $geoResponse = Http::timeout(10)->get('https://geocoding-api.open-meteo.com/v1/search', [
            'name' => $cityName,
            'count' => 1,
            'language' => 'pt',
            'format' => 'json',
        ]);

        if ($geoResponse->failed() || empty($geoResponse->json('results'))) {
            return response()->json([
                'error' => "Cidade '{$cityName}' não encontrada."
            ], 404);
        }

        $location = $geoResponse->json('results')[0];

        // 2. Com a latitude e longitude em mãos, busca o clima atual
        $weatherResponse = Http::timeout(10)->get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => $location['latitude'],
            'longitude' => $location['longitude'],
            'current_weather' => true,
            'timezone' => 'America/Sao_Paulo',
        ]);

        if ($weatherResponse->failed()) {
            return response()->json(['error' => 'Erro ao consultar o clima.'], 502);
        }

        $weather = $weatherResponse->json('current_weather');

        return response()->json([
            'localizacao' => [
                'cidade' => $location['name'],
                'estado' => $location['admin1'] ?? 'N/A',
                'pais' => $location['country'] ?? 'Brasil',
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
            ],
            'clima_atual' => [
                'temperatura_c' => $weather['temperature'],
                'velocidade_vento_kmh' => $weather['windspeed'],
                'eh_dia' => (bool) $weather['is_day'],
                'atualizado_em' => $weather['time'],
            ]
        ]);
    }
}
