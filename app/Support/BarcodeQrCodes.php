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

    /** Payload QR sama dengan barcode_id agar scan kamera (1D/2D) konsisten. */
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
