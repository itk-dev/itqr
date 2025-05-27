<?php

namespace App\Controller\Admin;

use App\Helper\DownloadHelper;
use App\Repository\QrRepository;
use App\Repository\QrVisualConfigRepository;
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
        private DownloadHelper           $downloadHelper,
        private QrRepository             $qrRepository,
        private QrVisualConfigRepository $qrVisualConfigRepository,
    )
    {
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
     */
    #[Route('/admin/generate-qr-codes', name: 'admin_generate_qr_codes', methods: ['POST'])]
    public function generateQrCode(Request $request): JsonResponse
    {
        // Extract data from the request
        $data = $request->request->all();
        $formName = $data['formName'];
        $downloadSettings = $data[$formName] ?? [];

        $selectedQrCodes = $data['selectedQrCodes'] ?? [];

        // Can be defined as such to prompt a qr example preview.
        if ($selectedQrCodes !== 'examplePreview') {
            $selectedQrCodes = json_decode($selectedQrCodes, true);
        }

        /*
        Extract logo from the request (When a new image is uploaded either from batch download page or design config page.
        ImageField places uploaded files in ['logo']['file']
        FileType places uploaded files in ['logo']
        */
        $logo = null;
        $formData = $request->files->get($formName);

        if ($formData && isset($formData['logo'])) {
            if ($formData['logo'] instanceof UploadedFile) {
                $logo = $formData['logo'];
            } elseif (isset($formData['logo']['file']) && $formData['logo']['file'] instanceof UploadedFile) {
                $logo = $formData['logo']['file'];
            }
        }


        // If a design is edited, it contains an id from where we can grab the entity.
        $entity = isset($downloadSettings['id']) && !$logo ? $this->qrVisualConfigRepository->findOneBy(['id' => $downloadSettings['id']]) : null;

        // Uploaded logo > logo from entity > logo from logoPath (only on batch download page)
        if ($entity && $entity->getLogo()) {
            $downloadSettings['logoPath'] = $_ENV['APP_BASE_UPLOAD_PATH'] . $entity->getLogo();
            $logo = $downloadSettings['logoPath'];
        } elseif (isset($downloadSettings['logoPath']) && !$logo instanceof UploadedFile) {
            $logo = $downloadSettings['logoPath'];
        }


        $size = (int)($downloadSettings['size'] ?? 400);
        $margin = (int)($downloadSettings['margin'] ?? 0);
        $backgroundColor = $downloadSettings['backgroundColor'] ?? '#ffffff';
        $backgroundColor = $this->downloadHelper->createColorFromHex($backgroundColor);
        $foregroundColor = $downloadSettings['foregroundColor'] ?? '#000000';
        $foregroundColor = $this->downloadHelper->createColorFromHex($foregroundColor);
        $labelText = $downloadSettings['labelText'] ?? '';
        $labelFont = $this->downloadHelper->createFontInterface((int)($downloadSettings['labelSize'] ?? 12));
        $labelTextColor = $downloadSettings['labelTextColor'] ?? '#000000';
        $labelTextColor = $this->downloadHelper->createColorFromHex($labelTextColor);
        $labelMargin = new Margin((int)($downloadSettings['labelMarginTop'] ?? 0), 0, (int)($downloadSettings['labelMarginBottom'] ?? 0), 0);
        $errorCorrectionLevel = [
            'low' => ErrorCorrectionLevel::Low,
            'medium' => ErrorCorrectionLevel::Medium,
            'quartile' => ErrorCorrectionLevel::Quartile,
            'high' => ErrorCorrectionLevel::High,
        ][$downloadSettings['errorCorrectionLevel'] ?? 'medium'] ?? ErrorCorrectionLevel::Medium;

        // Initialize the array for storing base64-encoded QR codes
        $data = [];

        if ($selectedQrCodes === 'examplePreview') {
            // Generate the QR Code using Endroid QR Code Builder
            $builder = new Builder();
            $result = $builder->build(
                data: "qr visual config example preview",
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: $errorCorrectionLevel,
                size: $size,
                margin: $margin,
                foregroundColor: $foregroundColor,
                backgroundColor: $backgroundColor,
                labelText: $labelText,
                labelFont: $labelFont,
                labelAlignment: LabelAlignment::Center,
                labelMargin: $labelMargin,
                labelTextColor: $labelTextColor,
                logoPath: $logo,
                logoPunchoutBackground: false,
            );

            // Convert the QR code image to base64 and add to the array
            $data['examplePreview'] = 'data:image/png;base64,' . base64_encode($result->getString());

        // Respond with the array of QR codes as base64-encoded PNGs
        return new JsonResponse([
            'qrCodes' => $data,
        ]);
        }

        // Loop through each selected QR code entity ID
        foreach ($selectedQrCodes as $qrCodeId) {
            // Replace this with logic to retrieve the URL (or string) for each QR code entity
            $qrCodeUrl = $this->qrRepository->findOneBy(['id' => $qrCodeId])->getUrls()->first()->getUrl();
            $qrCodeTitle = $this->qrRepository->findOneBy(['id' => $qrCodeId])->getTitle();

            if (!$qrCodeUrl) {
                continue;
            }

            // Generate the QR Code using Endroid QR Code Builder
            $builder = new Builder();
            $result = $builder->build(
                data: $qrCodeUrl,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: $errorCorrectionLevel,
                size: $size,
                margin: $margin,
                foregroundColor: $foregroundColor,
                backgroundColor: $backgroundColor,
                labelText: $labelText,
                labelFont: $labelFont,
                labelAlignment: LabelAlignment::Center,
                labelMargin: $labelMargin,
                labelTextColor: $labelTextColor,
                logoPath: $logo,
                logoPunchoutBackground: false,
            );

            // Convert the QR code image to base64 and add to the array
            $data[$qrCodeTitle] = 'data:image/png;base64,' . base64_encode($result->getString());
        }

        // Respond with the array of QR codes as base64-encoded PNGs
        return new JsonResponse([
            'qrCodes' => $data,
        ]);
    }

    /**
     * Retrieves a QR Visual Config by its ID.
     *
     * @param int $id The identifier of the QR Visual Config to retrieve.
     *
     * @return JsonResponse Returns a JSON response containing the QR Visual Config details
     *                      or an error message if the configuration is not found.
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
            'logo' => $config->getLogo() ? $_ENV['APP_BASE_UPLOAD_PATH'] . $config->getLogo() : null,
        ]);
    }

}
