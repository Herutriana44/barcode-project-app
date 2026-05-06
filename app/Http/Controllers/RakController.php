<?php

namespace App\Http\Controllers;

use App\Models\Rak;
use Illuminate\Http\Request;

class RakController extends Controller
{
    private const ALLOWED_RAK_CODES = ['E1', 'E2', 'E3'];

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

        // Ambil data rak yang ada di database untuk perusahaan tersebut
        $dbCodes = Rak::query()
            ->whereRaw('LOWER(TRIM(company_name)) = ?', [mb_strtolower($companyName)])
            ->pluck('code')
            ->map(function ($v) {
                return strtoupper(trim((string) $v));
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        // Gabungkan dengan daftar yang diizinkan (atau batasi hanya pada daftar yang diizinkan)
        // Kita gunakan intersection agar hanya kode yang ada di DB DAN diizinkan yang muncul
        $codes = array_values(array_intersect($dbCodes, self::ALLOWED_RAK_CODES));

        // Jika array kosong setelah filter, namun kita ingin tetap menampilkan pilihan E1, E2, E3 
        // (opsional: tergantung kebutuhan bisnis, namun biasanya yang ada di DB saja)
        // Jika user ingin E1, E2, E3 selalu muncul meski di DB belum ada, logika harus diubah.

        // Sort natural untuk format seperti "B4, B6, C10"
        usort($codes, function (string $a, string $b) {
            $pa = $this->parseRakCode($a);
            $pb = $this->parseRakCode($b);

            if ($pa === null && $pb === null) return $a <=> $b;
            if ($pa === null) return 1;
            if ($pb === null) return -1;

            [$la, $na] = $pa;
            [$lb, $nb] = $pb;
            if ($la !== $lb) return $la <=> $lb;
            if ($na !== $nb) return $na <=> $nb;
            return $a <=> $b;
        });

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

