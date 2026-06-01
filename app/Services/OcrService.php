<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class OcrService
{
    public function analyze(mixed $state): array
    {
        $endpoint = config('services.azure_di.endpoint');
        $headers = ['Ocp-Apim-Subscription-Key' => config('services.azure_di.key')];

        $response = Http::withHeaders($headers)
            ->post("$endpoint/formrecognizer/documentModels/prebuilt-read:analyze?api-version=2023-07-31", [
                'base64Source' => base64_encode(
                    is_string($state)
                        ? Storage::disk('local')->get($state)
                        : file_get_contents($state->getRealPath())
                ),
            ]);

        do {
            sleep(1);
            $result = Http::withHeaders($headers)->get($response->header('Operation-Location'))->json();
        } while ($result['status'] !== 'succeeded');

        $lines = collect(explode("\n", $result['analyzeResult']['content']))
            ->map('trim')
            ->values();

        return [
            'name' => $lines->first(),
            'description' => $lines->slice(1, 3)->implode(' '),
        ];
    }
}
