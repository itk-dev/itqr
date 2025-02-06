<?php

namespace App\Helper;

use App\Entity\Qr;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadHelper
{
    /**
     * Generate and download QR Codes for multiple entities.
     *
     * @param array $qrEntities Array of QR entities (should have UUID or equivalent identification method).
     * @param array $settings Settings for QR code generation (e.g., size).
     *
     * @return StreamedResponse
     */
    public function generateQrCodes(array $qrEntities, array $settings): StreamedResponse
    {
// Extract or set a default size
        $size = $settings['size'] ?? 400;

// Check if there's only one entity
        if (count($qrEntities) === 1) {
            die('f');
// Single QR code generation
            $qrEntity = reset($qrEntities);
            return $this->generateSingleQrCode($qrEntity, $size);
        }

// Generate multiple QR codes as a ZIP file
        return $this->generateQrCodesAsZip($qrEntities, $size);
    }

    /**
     * Generate a single QR Code for download as PNG.
     */
    private function generateSingleQrCode($qrEntity, int $size): StreamedResponse
    {
        $uuid = $qrEntity->getUuid();
        $qrContent = $_ENV['APP_BASE_REDIRECT_PATH'] . $uuid;

        $renderer = new ImageRenderer(
            new RendererStyle($size),
            new ImagickImageBackEnd('png')
        );

        $writer = new Writer($renderer);
        $qrCodeData = $writer->writeString($qrContent);

// Prepare response
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
    private function generateQrCodesAsZip(array $qrEntities, int $size): StreamedResponse
    {
        $zipFilename = tempnam(sys_get_temp_dir(), 'qrcodes') . '.zip';
        $zip = new \ZipArchive();

        if ($zip->open($zipFilename, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Cannot create ZIP file.');
        }

        foreach ($qrEntities as $qrEntity) {
            $uuid = $qrEntity->getUuid();
            $qrContent = $_ENV['APP_BASE_REDIRECT_PATH'] . $uuid;

// Generate QR code image
            $renderer = new ImageRenderer(
                new RendererStyle($size),
                new ImagickImageBackEnd('png')
            );
            $writer = new Writer($renderer);
            $qrCodeData = $writer->writeString($qrContent);

// Add QR code to the ZIP file
            $zip->addFromString("qr_code_$uuid.png", $qrCodeData);
        }

// Close the ZIP archive
        $zip->close();

// Prepare response for the ZIP
        $response = new StreamedResponse(function () use ($zipFilename) {
            readfile($zipFilename);
            unlink($zipFilename); // Cleanup the temp file after download
        });

        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-Disposition', 'attachment; filename="qr_codes.zip"');

        return $response;
    }
}
