<?php

namespace App\Controller;

use App\Helper\DownloadHelper;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Exception\ValidationException;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Margin\Margin;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

readonly class QrCodePreviewController
{
    public function __construct(
        private DownloadHelper $downloadHelper,
    ) {
    }

    /**
     * Handles the generation of a QR codes for review before batch download.
     * Provides the QR code as a base64-encoded PNG in the response.
     *
     * @param Request $request the HTTP request containing parameters for the QR code
     *
     * @return JsonResponse a JSON response containing the generated QR code as a base64-encoded string
     *
     * @throws ValidationException
     */
    #[Route('/generate-qr-code', name: 'generate_qr_code', methods: ['POST'])]
    public function generateQrCode(Request $request): JsonResponse
    {
        // Extract data from the request
        $data = $request->request->all();

        $downloadSettings = $data['batch_download'] ?? [];

        $logo = $request->files->get('batch_download')['logo'] ?? null;

        if (!$logo instanceof UploadedFile) {
            $logo = null;
        }

        // Build the data you want encoded in the QR code
        $qrString = 'https://www.google.dk';

        // Get QR code settings or use defaults
        $size = (int) min(400, $downloadSettings['size'] ?? 400);
        $margin = (int) ($downloadSettings['margin'] ?? 0);
        $backgroundColor = $downloadSettings['backgroundColor'] ?? '#ffffff';
        $backgroundColor = $this->downloadHelper->createColorFromHex($backgroundColor);
        $foregroundColor = $downloadSettings['foregroundColor'] ?? '#000000';
        $foregroundColor = $this->downloadHelper->createColorFromHex($foregroundColor);
        $labelText = $downloadSettings['labelText'] ?? '';
        $labelTextColor = $downloadSettings['labelTextColor'] ?? '#000000';
        $labelTextColor = $this->downloadHelper->createColorFromHex($labelTextColor);
        $labelMargin = new Margin((int) $downloadSettings['labelMarginTop'] ?: 0, 0, (int) $downloadSettings['labelMarginBottom'] ?: 0, 0);
        $errorCorrectionLevel = [
            'low' => ErrorCorrectionLevel::Low,
            'medium' => ErrorCorrectionLevel::Medium,
            'quartile' => ErrorCorrectionLevel::Quartile,
            'high' => ErrorCorrectionLevel::High,
        ][$downloadSettings['errorCorrectionLevel'] ?? 'medium'] ?? ErrorCorrectionLevel::Medium;

        // Generate the QR Code using Endroid QR Code Builder
        $builder = new Builder();
        $result = $builder->build(
            data: $qrString,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: $errorCorrectionLevel,
            size: $size,
            margin: $margin,
            foregroundColor: $foregroundColor,
            backgroundColor: $backgroundColor,
            labelText: $labelText,
            labelAlignment: LabelAlignment::Center,
            labelMargin: $labelMargin,
            labelTextColor: $labelTextColor,
            logoPath: $logo,
            logoPunchoutBackground: false,
        );

        // Convert the QR code image to base64
        $qrCodeBase64 = base64_encode($result->getString());

        // Respond with the QR code as a base64-encoded PNG
        return new JsonResponse([
            'qrCode' => 'data:image/png;base64,'.$qrCodeBase64,
        ]);
    }
}
