<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class ExternalDataController extends Controller
{
    private const DATA_URL = 'https://ogienurdiana.com/career/ecc694ce4e7f6e45a5a7912cde9fe131';

    public function searchByName(string $name)
    {
        return $this->searchByField('NAMA', $name);
    }

    public function searchByNim(string $nim)
    {
        return $this->searchByField('NIM', $nim);
    }

    public function searchByYmd(string $ymd)
    {
        return $this->searchByField('YMD', $ymd);
    }

    private function searchByField(string $field, string $value)
    {
        $records = $this->fetchRecords();

        if ($records instanceof \Illuminate\Http\JsonResponse) {
            return $records;
        }

        $matches = array_values(array_filter($records, function (array $row) use ($field, $value) {
            return isset($row[$field]) && $row[$field] === $value;
        }));

        return response()->json([
            'field' => $field,
            'value' => $value,
            'count' => count($matches),
            'data' => $matches,
        ]);
    }

    private function fetchRecords()
    {
        $response = Http::timeout(15)->get(self::DATA_URL);

        if (! $response->ok()) {
            return response()->json(['message' => 'Failed to fetch external data'], 502);
        }

        $payload = $response->json();
        if (! is_array($payload)) {
            $payload = json_decode($response->body(), true);
        }

        $dataString = is_array($payload) ? ($payload['DATA'] ?? null) : null;
        if (! is_string($dataString)) {
            return response()->json(['message' => 'Invalid external data format'], 502);
        }

        $lines = preg_split('/\r?\n/', trim($dataString));
        if (! $lines || count($lines) < 2) {
            return response()->json(['message' => 'External data is empty'], 502);
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
