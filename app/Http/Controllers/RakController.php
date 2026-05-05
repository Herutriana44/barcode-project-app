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

        $codes = Rak::query()
            ->whereRaw('LOWER(TRIM(company_name)) = ?', [mb_strtolower($companyName)])
            ->pluck('code')
            ->values()
            ->map(function ($v) {
                $s = strtoupper(trim((string) $v));
                return $s === '' ? null : $s;
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        // Sort natural untuk format seperti "B4, B6, C10"
        usort($codes, function (string $a, string $b) {
            $pa = $this->parseRakCode($a);
            $pb = $this->parseRakCode($b);

            // Unknown format taruh di bawah tapi tetap stabil
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

