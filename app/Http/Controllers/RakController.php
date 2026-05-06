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

        // Ambil semua code sebagai string dari database
        $rows = Rak::query()
            ->whereRaw('LOWER(TRIM(company_name)) = ?', [mb_strtolower($companyName)])
            ->pluck('code');

        $allCodes = [];
        foreach ($rows as $row) {
            // Pecah berdasarkan koma jika ada, trim, dan ambil yang tidak kosong
            $parts = explode(',', (string) $row);
            foreach ($parts as $part) {
                $code = trim($part);
                if ($code !== '') {
                    $allCodes[] = $code;
                }
            }
        }

        // Ambil nilai unik dan urutkan secara alfabetis
        $uniqueCodes = array_unique($allCodes);
        sort($uniqueCodes);

        return response()->json(['codes' => array_values($uniqueCodes)]);
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

