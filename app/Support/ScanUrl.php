<?php

namespace App\Support;

final class ScanUrl
{
    /**
     * URL publik ke halaman hasil scan untuk ID barcode (IB-… / CB-…).
     * Dipakai di QR dan Code 128 agar pemindai ponsel bisa membuka langsung ke aplikasi.
     */
    public static function forBarcode(string $barcodeId): string
    {
        if (env('SCAN_MODE') === 'public') {
            return url('/public/scan/' . $barcodeId);
        }

        return route('scan.show', ['barcode_id' => $barcodeId], absolute: true);
    }
}
