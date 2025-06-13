<?php

namespace App\Controller\Admin;

use App\Repository\QrVisualConfigRepository;
use App\Service\QrCodePreviewService;
use Endroid\QrCode\Exception\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

readonly class QrCodePreviewController
{
    public function __construct(
        private QrVisualConfigRepository $qrVisualConfigRepository,
        private QrCodePreviewService $qrCodePreviewService,
    ) {
    }

    /**
     * Handles the generation of QR codes for multiple selected entities.
     * Returns the QR codes as base64-encoded strings in an array.
     *
     * Handles preview generation for both configuration of designs and batch download page.
     *
     * @param Request $request the HTTP request containing parameters for the QR codes
     *
     * @return JsonResponse a JSON response containing the generated QR codes as a base64-encoded array
     *
     * @throws ValidationException
     * @throws \Exception
     */
    #[Route('/admin/generate-qr-codes', name: 'admin_generate_qr_codes', methods: ['POST'])]
    public function generateQrCode(Request $request): JsonResponse
    {
        // Send request data to service to handle generation
        return $this->qrCodePreviewService->generateQrCode($request);
    }

    /**
     * Retrieves a QR Visual Config by its ID.
     *
     * @param int $id the identifier of the QR Visual Config to retrieve
     *
     * @return JsonResponse returns a JSON response containing the QR Visual Config details
     *                      or an error message if the configuration is not found
     */
    #[Route('/admin/qr_visual_configs/{id}', name: 'admin_qr_visual_config_get', methods: ['GET'])]
    public function getQrVisualConfig(int $id): JsonResponse
    {
        $config = $this->qrVisualConfigRepository->findOneBy(['id' => $id]);

        if (!$config) {
            return new JsonResponse(['error' => 'QR Visual Config not found'], 404);
        }

        return new JsonResponse([
            'id' => $config->getId(),
            'size' => $config->getSize(),
            'margin' => $config->getMargin(),
            'backgroundColor' => $config->getBackgroundColor(),
            'foregroundColor' => $config->getForegroundColor(),
            'labelText' => $config->getLabelText(),
            'labelSize' => $config->getLabelSize(),
            'labelTextColor' => $config->getLabelTextColor(),
            'labelMarginTop' => $config->getLabelMarginTop(),
            'labelMarginBottom' => $config->getLabelMarginBottom(),
            'errorCorrectionLevel' => $config->getErrorCorrectionLevel()->value,
            'logo' => $config->getLogo() ? $_ENV['APP_BASE_UPLOAD_PATH'].$config->getLogo() : null,
        ]);
    }
}
