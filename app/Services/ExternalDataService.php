<?php

namespace App\Services;

use App\Exceptions\ExternalDataException;
use Illuminate\Support\Facades\Http;

class ExternalDataService
{
    private string $url;
    private int $timeout;

    public function __construct()
    {
        $this->url = config('external.url');
        $this->timeout = (int) config('external.timeout', 15);
    }

    /**
     * Cari data berdasarkan field tertentu.
     *
     * @param  string  $field
     * @param  string  $value
     * @return array{field: string, value: string, count: int, data: array}
     * @throws ExternalDataException
     */
    public function searchByField(string $field, string $value): array
    {
        $records = $this->fetchRecords();

        $matches = array_values(
            array_filter($records, fn (array $row) => ($row[$field] ?? null) === $value)
        );

        return [
            'field' => $field,
            'value' => $value,
            'count' => count($matches),
            'data' => $matches,
        ];
    }

    /**
     * @return array<int, array<string, string>>
     * @throws ExternalDataException
     */
    private function fetchRecords(): array
    {
        $response = Http::timeout($this->timeout)->get($this->url);

        if (! $response->ok()) {
            throw new ExternalDataException('Gagal mengambil data dari sumber eksternal.', 502);
        }

        $payload = $response->json();

        if (! is_array($payload)) {
            $payload = json_decode($response->body(), true);
        }

        $dataString = is_array($payload) ? ($payload['DATA'] ?? null) : null;

        if (! is_string($dataString)) {
            throw new ExternalDataException('Format data eksternal tidak valid.', 502);
        }

        return $this->parseRecords($dataString);
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function parseRecords(string $raw): array
    {
        $lines = preg_split('/\r?\n/', trim($raw));

        if (! $lines || count($lines) < 2) {
            throw new ExternalDataException('Data eksternal kosong.', 502);
        }

        $headers = explode('|', array_shift($lines));
        $records = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            $values = explode('|', $line);

            if (count($values) !== count($headers)) {
                continue;
            }

            $records[] = array_combine($headers, $values);
        }

        return $records;
    }
}
