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

        // Ambil semua code sebagai array
        $rows = Rak::query()
            ->whereRaw('LOWER(TRIM(company_name)) = ?', [mb_strtolower($companyName)])
            ->pluck('code');

        $allCodes = [];
        foreach ($rows as $row) {
            // Kita pastikan string diproses dengan benar. 
            // Jika row mengandung koma, explode akan memecahnya.
            // Kita juga tangani jika ada spasi di sekitar koma.
            $parts = explode(',', (string) $row);
            foreach ($parts as $part) {
                $code = trim($part);
                if ($code !== '') {
                    $allCodes[] = $code;
                }
            }
        }

        // Ambil nilai unik dan urutkan
        $uniqueCodes = array_unique($allCodes);
        sort($uniqueCodes);
        $finalCodes = array_values($uniqueCodes);

        return response()->json([
            'codes' => $finalCodes,
            'raw_data' => $rows->toArray(),
            'parsed_codes' => $finalCodes
        ]);
    }

    /**
     * Ambil semua opsi rak tanpa filter perusahaan.
     *
     * @return \Illuminate\Http\JsonResponse
     */
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
        $finalCodes = array_values($uniqueCodes);

        return response()->json(['codes' => $finalCodes]);
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

