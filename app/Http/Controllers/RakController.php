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
            return response()->json(['codes' => []]);
        }

        // Ambil semua code sebagai string, lalu pecah berdasarkan koma
        $rawCodes = Rak::query()
            ->whereRaw('LOWER(TRIM(company_name)) = ?', [mb_strtolower($companyName)])
            ->pluck('code')
            ->toArray();

        $allCodes = [];
        foreach ($rawCodes as $row) {
            // Pecah string "E1, E2, E3" menjadi array ['E1', 'E2', 'E3']
            $parts = explode(',', (string) $row);
            foreach ($parts as $p) {
                $trimmed = strtoupper(trim($p));
                if ($trimmed !== '') {
                    $allCodes[] = $trimmed;
                }
            }
        }

        // Ambil nilai unik
        $codes = array_values(array_unique($allCodes));
        
        // Cukup sort secara alfabetis standar agar konsisten
        sort($codes);

        return response()->json(['codes' => $codes]);
    }

    /**
     * @return array{string,int}|null
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

