<?php

namespace App\Helper;

use App\Entity\Tenant\Qr;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Exception\ValidationException;
use Endroid\QrCode\Label\Font\Font;
use Endroid\QrCode\Label\Font\FontInterface;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Margin\Margin;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DownloadHelper
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * Generate and download QR Codes for multiple entities.
     *
     * @param array<int,Qr>       $qrEntities
     *                                              An array of QR entities
     * @param array<string,mixed> $downloadSettings
     *                                              Settings for QR Code generation
     *
     * @throws ValidationException
     */
    public function generateQrCodes(array $qrEntities, array $downloadSettings): StreamedResponse
    {
        // Map settings
        $settings = [
            'size' => (int) ($downloadSettings['size'] ?? 400),
            'margin' => (int) ($downloadSettings['margin'] ?? 10),
            'backgroundColor' => $this->createColorFromHex($downloadSettings['backgroundColor'] ?? '#ffffff'),
            'foregroundColor' => $this->createColorFromHex($downloadSettings['foregroundColor'] ?? '#000000'),
            'labelText' => $downloadSettings['labelText'] ?? '',
            'labelFont' => $this->createFontInterface((int) ($downloadSettings['labelSize'] ?? 12)),
            'labelTextColor' => $this->createColorFromHex($downloadSettings['labelTextColor'] ?? '#000000'),
            'labelMargin' => $this->createLabelMargin((int) ($downloadSettings['labelMarginTop'] ?? 0), (int) ($downloadSettings['labelMarginBottom'] ?? 0)),
            'errorCorrectionLevel' => [
                'low' => ErrorCorrectionLevel::Low,
                'medium' => ErrorCorrectionLevel::Medium,
                'quartile' => ErrorCorrectionLevel::Quartile,
                'high' => ErrorCorrectionLevel::High,
            ][$downloadSettings['errorCorrectionLevel'] ?? 'medium'] ?? ErrorCorrectionLevel::Medium,
            'logo' => $this->processLogo($downloadSettings['logo'] ?? null) ?? $downloadSettings['logoPath'] ?? null,
        ];

        // Based on the number of entities, call the appropriate function
        return 1 === count($qrEntities)
            ? $this->generateSingleQrCode(reset($qrEntities), $settings)
            : $this->generateQrCodesAsZip($qrEntities, $settings);
    }

    /**
     * Generate a single QR Code for download as PNG.
     *
     * @param Qr                  $qrEntity
     *                                      A Qr code
     * @param array<string,mixed> $settings
     *                                      Some qr settings
     *
     * @throws ValidationException
     */
    private function generateSingleQrCode(
        Qr $qrEntity,
        array $settings,
    ): StreamedResponse {
        // Build qr code with given settings
        $qrCodeData = $this->buildQrCode($qrEntity, $settings);

        // Prepare the HTTP response for downloading the image
        $response = new StreamedResponse(function () use ($qrCodeData) {
            echo $qrCodeData;
        });

        // Set headers
        $response->headers->set('Content-Type', 'image/png');
        $response->headers->set('Content-Disposition', 'attachment; filename="qr_code.png"');

        return $response;
    }

    /**
     * Generate multiple QR codes and download them as a ZIP file.
     *
     * @param array<int,Qr>       $qrEntities
     *                                        An array of QR entities
     * @param array<string,mixed> $settings
     *                                        Some qr settings
     *
     * @throws ValidationException
     */
    private function generateQrCodesAsZip(
        array $qrEntities,
        array $settings,
    ): StreamedResponse {
        $zipFilename = tempnam(sys_get_temp_dir(), 'qrcodes').'.zip';
        $zip = new \ZipArchive();

        if (true !== $zip->open($zipFilename, \ZipArchive::CREATE | \ZipArchive::OVERWRITE)) {
            throw new \RuntimeException('Cannot create ZIP file.');
        }

        foreach ($qrEntities as $qrEntity) {
            $qrCodeData = $this->buildQrCode($qrEntity, $settings);
            $uuid = $qrEntity->getUuid();

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
     * Build a QR code using the provided entity and settings.
     *
     * @param Qr                  $qrEntity
     *                                      A Qr code
     * @param array<string,mixed> $settings
     *                                      Some qr settings
     *
     * @throws ValidationException
     */
    private function buildQrCode(
        Qr $qrEntity,
        array $settings,
    ): string {
        $uuid = $qrEntity->getUuid();
        $qrContent = $this->urlGenerator->generate('app_qr_index', ['uuid' => $uuid]);

        // Use the Endroid QR Code Builder to generate the QR Code
        $result = (new Builder())->build(
            data: $qrContent,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: $settings['errorCorrectionLevel'],
            size: $settings['size'],
            margin: $settings['margin'],
            foregroundColor: $settings['foregroundColor'],
            backgroundColor: $settings['backgroundColor'],
            labelText: $settings['labelText'],
            labelFont: $settings['labelFont'],
            labelAlignment: LabelAlignment::Center,
            labelMargin: $settings['labelMargin'],
            labelTextColor: $settings['labelTextColor'],
            logoPath: $settings['logo'] ?? $settings['logoPath'],
            logoPunchoutBackground: false,
        );

        return $result->getString(); // Return QR code as binary string (PNG format)
    }

    /**
     * Process the logo file for QR code generation.
     */
    public function processLogo(?UploadedFile $logo): ?string
    {
        if ($logo instanceof UploadedFile) {
            // Save the uploaded file and return its path
            $targetPath = sys_get_temp_dir().'/'.$logo->getClientOriginalName();
            $logo->move(sys_get_temp_dir(), $logo->getClientOriginalName());

            return $targetPath;
        }

        return null; // No logo provided
    }

    /**
     * Converts a hexadecimal color code to an instance of the Color class.
     *
     * @param string $hexColor
     *                         The hexadecimal color code
     *
     * @return Color
     *               An instance of the Color class
     */
    public function createColorFromHex(string $hexColor): Color
    {
        [$r, $g, $b] = sscanf($hexColor, '#%02x%02x%02x');

        return new Color($r, $g, $b);
    }

    /**
     * Create a margin object with specified top and bottom margins.
     *
     * @param int $top Top margin value
     * @param int $bottom Bottom margin value
     */
    public function createLabelMargin(int $top, int $bottom): Margin
    {
        return new Margin($top, 0, $bottom, 0);
    }

    /**
     * Create a font interface with the specified font size.
     *
     * @param int $size The font size
     * @return FontInterface The created font interface
     */
    public function createFontInterface(int $size): FontInterface
    {
        return new Font((new OpenSans())->getPath(), $size);
    }

}
