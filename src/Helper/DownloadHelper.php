<?php

namespace App\Helper;

use App\Entity\Qr;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadHelper
{
    /**
     * Generate and download QR Codes for multiple entities.
     *
     * @param array $qrEntities An array of QR entities (containing UUID or identifiers)
     * @param array $settings Settings for QR Code generation (size, margin, colors)
     */
    public function generateQrCodes(array $qrEntities, array $settings): StreamedResponse
    {
        // Extract or set default settings
        $size = $settings['size'] ?? 400;
        $margin = $settings['margin'] ?? 10;
        $foregroundColor = $this->createColorFromHex($settings['foregroundColor'] ?? '#000000');
        $backgroundColor = $this->createColorFromHex($settings['backgroundColor'] ?? '#FFFFFF');

        // Check if there's only one entity
        if (1 === count($qrEntities)) {
            $qrEntity = reset($qrEntities);

            return $this->generateSingleQrCode($qrEntity, $size, $margin, $foregroundColor, $backgroundColor);
        }

        // Generate multiple QR codes as a ZIP file
        return $this->generateQrCodesAsZip($qrEntities, $size, $margin, $foregroundColor, $backgroundColor);
    }

    /**
     * Generate a single QR Code for download as PNG.
     */
    private function generateSingleQrCode(
        Qr $qrEntity,
        int $size,
        int $margin,
        Color $foregroundColor,
        Color $backgroundColor
    ): StreamedResponse {
        $uuid = $qrEntity->getUuid();
        $qrContent = $_ENV['APP_BASE_REDIRECT_PATH'] . $uuid;

        // Use the Endroid QR Code Builder to generate the QR Code
        $result = Builder::create()->build(
            data: $qrContent,
            encoding: new Encoding('UTF-8'),
            size: $size,
            margin: $margin,
            foregroundColor: $foregroundColor,
            backgroundColor: $backgroundColor
        );

        $qrCodeData = $result->getString(); // Get QR code as binary string (PNG format)

        // Prepare the HTTP response for downloading the image
        $response = new StreamedResponse(function () use ($qrCodeData) {
            echo $qrCodeData;
        });

        $response->headers->set('Content-Type', 'image/png');
        $response->headers->set('Content-Disposition', 'attachment; filename="qr_code.png"');

        return $response;
    }

    /**
     * Generate multiple QR codes and download them as a ZIP file.
     */
    private function generateQrCodesAsZip(
        array $qrEntities,
        int $size,
        int $margin,
        Color $foregroundColor,
        Color $backgroundColor
    ): StreamedResponse {
        $zipFilename = tempnam(sys_get_temp_dir(), 'qrcodes') . '.zip';
        $zip = new \ZipArchive();

        if (true !== $zip->open($zipFilename, \ZipArchive::CREATE | \ZipArchive::OVERWRITE)) {
            throw new \RuntimeException('Cannot create ZIP file.');
        }

        foreach ($qrEntities as $qrEntity) {
            $uuid = $qrEntity->getUuid();
            $qrContent = $_ENV['APP_BASE_REDIRECT_PATH'] . $uuid;

            $builder = new Builder();
            // Use the Endroid QR Code Builder to generate the QR Code
            $result = $builder->build(
                data: $qrContent,
                encoding: new Encoding('UTF-8'),
                size: $size,
                margin: $margin,
                foregroundColor: $foregroundColor,
                backgroundColor: $backgroundColor
            );

            $qrCodeData = $result->getString(); // QR code as binary string (PNG format)

            // Add the QR code image to the ZIP file
            $zip->addFromString("qr_code_$uuid.png", $qrCodeData);
        }

        // Close the ZIP archive
        $zip->close();

        // Prepare the HTTP response for downloading the ZIP file
        $response = new StreamedResponse(function () use ($zipFilename) {
            readfile($zipFilename);
            unlink($zipFilename); // Clean up temporary file after the response is sent
        });

        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-Disposition', 'attachment; filename="qr_codes.zip"');

        return $response;
    }

    /**
     * Converts a HEX color string to an Endroid Color object.
     */
    private function createColorFromHex(string $hexColor): Color
    {
        list($r, $g, $b) = sscanf($hexColor, "#%02x%02x%02x");
        return new Color($r, $g, $b);
    }
}
