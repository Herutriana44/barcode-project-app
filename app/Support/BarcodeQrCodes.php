<?php

namespace App\Support;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\SvgWriter;
use Picqer\Barcode\BarcodeGeneratorSVG;

final class BarcodeQrCodes
{
    public static function code128Svg(string $payload, int $widthFactor = 2, int $height = 50): string
    {
        $generator = new BarcodeGeneratorSVG;

        return $generator->getBarcode($payload, $generator::TYPE_CODE_128, $widthFactor, $height);
    }

    /** QR berisi URL scan agar pemindai ponsel membuka halaman barang di browser. */
    public static function qrSvgForScan(string $barcodeId, int $size = 180, int $margin = 8): string
    {
        return self::qrSvg(ScanUrl::forBarcode($barcodeId), $size, $margin);
    }

    /** Code 128 berisi URL yang sama agar pemindai garis mengirim tautan penuh. */
    public static function code128SvgForScan(string $barcodeId, int $widthFactor = 2, int $height = 50): string
    {
        $url = ScanUrl::forBarcode($barcodeId);
        if (strlen($url) > 96) {
            $widthFactor = 1;
        }

        return self::code128Svg($url, max(1, $widthFactor), $height);
    }

    public static function qrSvg(string $payload, int $size = 180, int $margin = 8): string
    {
        return Builder::create()
            ->writer(new SvgWriter)
            ->writerOptions([
                SvgWriter::WRITER_OPTION_EXCLUDE_XML_DECLARATION => true,
            ])
            ->data($payload)
            ->size($size)
            ->margin($margin)
            ->build()
            ->getString();
    }
}
