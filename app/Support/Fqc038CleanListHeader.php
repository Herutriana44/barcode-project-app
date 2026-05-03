<?php

namespace App\Support;

/**
 * Normalisasi header sheet Excel clean_list_part (FQC-038) untuk import & seeder.
 */
final class Fqc038CleanListHeader
{
    public static function normalizeHeader(string $h): string
    {
        $h = str_replace(["\r", "\n", "\t"], ' ', $h);
        $h = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}\x{2060}]/u', '', $h) ?? $h;
        $h = preg_replace('/\s+/', ' ', $h) ?? $h;

        return mb_strtolower(trim($h));
    }

    public static function canonicalHeader(string $h): string
    {
        $n = self::normalizeHeader($h);
        if ($n === '' || $n === '-' || $n === '—') {
            return '';
        }

        $n = str_replace(
            ['（', '）', '［', '］', '：', '．', '，'],
            ['(', ')', '[', ']', ':', '.', ','],
            $n
        );

        $n = str_replace(['.', ':'], '', $n);

        $map = [
            'part code' => 'part_code',
            'partcode' => 'part_code',
            'kode part' => 'part_code',
            'code' => 'part_code',
            'current part no' => 'part_no',
            'current part number' => 'part_no',
            'current part no ' => 'part_no',
            'part no' => 'part_no',
            'part number' => 'part_no',
            'part number ' => 'part_no',
            'current part no/part number' => 'part_no',
            'part description' => 'part_description',
            'description' => 'part_description',
            'qty/pack(pcs)' => 'qty_pack_pcs',
            'qty/pack (pcs)' => 'qty_pack_pcs',
            'qty/pack' => 'qty_pack_pcs',
            'qty pack(pcs)' => 'qty_pack_pcs',
            'qty pack' => 'qty_pack_pcs',
            'customer' => 'customer',
            'cust' => 'customer',
            'qty sub pack' => 'qty_sub_pack_pcs',
            'qty sub pack(pcs)' => 'qty_sub_pack_pcs',
            'qty sub pack (pcs)' => 'qty_sub_pack_pcs',
            'qty subpack' => 'qty_sub_pack_pcs',
            'qty sub-pack' => 'qty_sub_pack_pcs',
            'qty sub pack pcs' => 'qty_sub_pack_pcs',
            'isi per box (pcs)' => 'qty_sub_pack_pcs',
            'isi per box' => 'qty_sub_pack_pcs',
            'berat packaging(gram)' => 'berat_packaging_gram',
            'berat packaging (gram)' => 'berat_packaging_gram',
            'berat packaging gram' => 'berat_packaging_gram',
            'berat packaging (g)' => 'berat_packaging_gram',
            'berat packaging(g)' => 'berat_packaging_gram',
            'berat kemasan (gram)' => 'berat_packaging_gram',
            'berat kemasan (g)' => 'berat_packaging_gram',
            'berat kemasan gram' => 'berat_packaging_gram',
            'berat dus (gram)' => 'berat_packaging_gram',
            'berat box (gram)' => 'berat_packaging_gram',
            'bw packaging (gram)' => 'berat_packaging_gram',
            'berat per pcs(gram)' => 'berat_per_pcs_gram',
            'berat per pcs (gram)' => 'berat_per_pcs_gram',
            'berat per pcs gram' => 'berat_per_pcs_gram',
            'berat per pcs (g)' => 'berat_per_pcs_gram',
            'berat per pcs(g)' => 'berat_per_pcs_gram',
            'berat pcs (gram)' => 'berat_per_pcs_gram',
            'berat/pcs (gram)' => 'berat_per_pcs_gram',
            'berat / pcs (gram)' => 'berat_per_pcs_gram',
            'berat nett/pcs (gram)' => 'berat_per_pcs_gram',
            'prod date' => 'prod_date',
            'prod. date' => 'prod_date',
            'production date' => 'prod_date',
            'exp date' => 'exp_date',
            'exp. date' => 'exp_date',
            'expired date' => 'exp_date',
            'berat total(kg)' => 'berat_total_kg',
            'berat total (kg)' => 'berat_total_kg',
            'berat total kg' => 'berat_total_kg',
            'berat total' => 'berat_total_kg',
            'berat (kg)' => 'berat_total_kg',
            'berat kg' => 'berat_total_kg',
            'berat' => 'berat_total_kg',
            'weight (kg)' => 'berat_total_kg',
            'weight total (kg)' => 'berat_total_kg',
            'model' => 'model',
            'rak' => 'rak',
            'rack' => 'rak',
            'posisi rak' => 'rak',
            'posisi rack' => 'rak',
            'lokasi rak' => 'rak',
        ];

        if (isset($map[$n])) {
            return $map[$n];
        }

        if (str_contains($n, 'berat') && str_contains($n, 'packaging') && (str_contains($n, 'gram') || str_contains($n, '(g)'))) {
            return 'berat_packaging_gram';
        }
        if (str_contains($n, 'berat')
            && (str_contains($n, 'per') && str_contains($n, 'pcs') || str_contains($n, '/pcs'))
            && (str_contains($n, 'gram') || str_contains($n, '(g)'))) {
            return 'berat_per_pcs_gram';
        }
        if (str_contains($n, 'berat') && str_contains($n, 'total')) {
            return 'berat_total_kg';
        }

        return $n;
    }

    /**
     * @param  array<int, string>  $maybeHeader
     */
    public static function looksLikeHeaderRow(array $maybeHeader): bool
    {
        $keys = array_filter($maybeHeader, fn ($k) => is_string($k) && $k !== '');
        if (count($keys) < 3) {
            return false;
        }

        $required = ['part_code', 'part_no', 'qty_pack_pcs', 'customer'];
        $hit = 0;
        foreach ($required as $r) {
            if (in_array($r, $keys, true)) {
                $hit++;
            }
        }

        return $hit >= 2;
    }
}
