<?php

namespace App\Controller;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Margin\Margin;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Endroid\QrCode\Builder\Builder;


class QrCodePreviewController
{
      #[Route('/generate-qr-code', name: 'generate_qr_code', methods: ['POST'])]
    public function generateQrCode(Request $request): JsonResponse
    {
        // Extract data from the request
        $data = $request->request->all();
        $downloadSettings = $data['batch_download'] ?? [];

        // Build the data you want encoded in the QR code
        $qrString = json_encode($data);

        // Get QR code settings or use defaults
        $size = (int) min(400, $downloadSettings['size'] ?? 400);
        $margin = (int)($downloadSettings['margin'] ?? 0);
        $backgroundColor = $downloadSettings['backgroundColor'] ?? '#ffffff';
        $backgroundColor = $this->createColorFromHex($backgroundColor);
        $foregroundColor = $downloadSettings['foregroundColor'] ?? '#000000';
        $foregroundColor = $this->createColorFromHex($foregroundColor);
        $labelText = $downloadSettings['labelText'] ?? '';
        $labelMargin = new Margin((int) $downloadSettings['labelMargin'] ?? 0, 0, (int) $downloadSettings['labelMargin'] ?? 0, 0);

        $builder = new Builder();
        // Generate the QR Code using Endroid QR Code Builder
        $result = $builder->build(
            data: $qrString,
            encoding: new Encoding('UTF-8'),
            size: $size,
            margin: $margin,
            foregroundColor: $foregroundColor,
            backgroundColor: $backgroundColor,
            labelText: $labelText,
            labelAlignment: LabelAlignment::Center,
            labelMargin: $labelMargin,
        );

        // Convert the QR code image to base64
        $qrCodeBase64 = base64_encode($result->getString());

        // Respond with the QR code as a base64-encoded PNG
        return new JsonResponse([
            'qrCode' => 'data:image/png;base64,' . $qrCodeBase64,
        ]);
    }


    private function createColorFromHex(string $hexColor): Color
    {
        list($r, $g, $b) = sscanf($hexColor, "#%02x%02x%02x");
        return new Color($r, $g, $b);
    }



}
