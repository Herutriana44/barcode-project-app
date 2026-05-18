<?php

namespace App\Http\Controllers;

use App\Models\Rak;
use Illuminate\Http\Request;

class RakController extends Controller
{
    /**
     * Ambil opsi rak berdasarkan nama perusahaan (case-insensitive).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function options(Request $request)
    {
        $companyName = trim((string) $request->query('company_name', ''));
        $path = base_path('perusahaan-rak.json');
        
        if (!file_exists($path)) {
            return response()->json(['codes' => [], 'raw_data' => [], 'parsed_codes' => []]);
        }

        $data = json_decode(file_get_contents($path), true);
        
        // Cari key yang cocok secara case-insensitive
        $foundCodes = [];
        foreach ($data as $key => $codes) {
            if (mb_strtolower(trim($key)) === mb_strtolower($companyName)) {
                $foundCodes = $codes;
                break;
            }
        }

        sort($foundCodes);
        return response()->json([
            'codes' => $foundCodes,
            'raw_data' => $foundCodes,
            'parsed_codes' => $foundCodes
        ]);
    }

    public function allOptions()
    {
        $path = base_path('perusahaan-rak.json');
        if (!file_exists($path)) {
            return response()->json(['codes' => []]);
        }

        $data = json_decode(file_get_contents($path), true);
        $allCodes = [];
        foreach ($data as $codes) {
            $allCodes = array_merge($allCodes, $codes);
        }

        $uniqueCodes = array_unique($allCodes);
        sort($uniqueCodes);
        
        return response()->json(['codes' => array_values($uniqueCodes)]);
    }

    /**
    private function parseRakCode(string $code): ?array
    {
        $s = strtoupper(trim($code));
        if ($s === '') return null;

        // Bentuk umum: "B4", "AA10", dst.
        if (preg_match('/^([A-Z]+)\s*(\d+)$/', $s, $m) !== 1) {
            return null;
        }

        return [$m[1], (int) $m[2]];
    }
}

