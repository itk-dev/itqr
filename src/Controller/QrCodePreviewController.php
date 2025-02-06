<?php

namespace App\Controller;

use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImageRendererFormat\Png;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class QrCodePreviewController
{
    #[Route('/generate-qr-code', name: 'generate_qr_code', methods: ['POST'])]
    public function generateQrCode(Request $request): JsonResponse
    {
        $data = $request->request->all(); // Get form data from the request

        $batchDownloadSettings = $data['batch_download'];
        // Build the data you want encoded in the QR code
        $qrString = json_encode($data);

        // Use Bacon QR Code to generate the QR code (using the ImageRenderer)
        $renderer = new ImageRenderer(
            new RendererStyle((int) $batchDownloadSettings['size'] ?: 400), // Set the size of the QR code (300px)
            new ImagickImageBackEnd('png')
        );
        $writer = new Writer($renderer);

        // Write the QR code to a binary string
        $qrCodeContent = $writer->writeString($qrString);

        // Convert image to base64 for embedding in the response
        $qrCodeBase64 = base64_encode($qrCodeContent);

        // Respond with the QR code as a base64-encoded PNG
        return new JsonResponse([
            'qrCode' => 'data:image/png;base64,' . $qrCodeBase64,
        ]);
    }
}
