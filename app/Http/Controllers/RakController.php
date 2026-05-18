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
        if ($companyName === '') {
            return response()->json(['codes' => [], 'raw_data' => [], 'parsed_codes' => []]);
        }

        $row = Rak::query()
            ->whereRaw('LOWER(TRIM(company_name)) = ?', [mb_strtolower($companyName)])
            ->first();

        $codes = [];
        if ($row && $row->code) {
            $parts = explode(',', (string) $row->code);
            foreach ($parts as $part) {
                $code = trim($part);
                if ($code !== '') {
                    $codes[] = $code;
                }
            }
        }

        sort($codes);
        return response()->json([
            'codes' => $codes,
            'raw_data' => $codes,
            'parsed_codes' => $codes
        ]);
    }

    public function allOptions()
    {
        $rows = Rak::query()->pluck('code');

        $allCodes = [];
        foreach ($rows as $row) {
            $parts = explode(',', (string) $row);
            foreach ($parts as $part) {
                $code = trim($part);
                if ($code !== '') {
                    $allCodes[] = $code;
                }
            }
        }

        $uniqueCodes = array_unique($allCodes);
        sort($uniqueCodes);
        
        return response()->json(['codes' => array_values($uniqueCodes)]);
    }

    /**
     * Parse rak code.
     */
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

