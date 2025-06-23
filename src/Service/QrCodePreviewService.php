<?php

namespace App\Service;

use App\DTO\DownloadSettingsDTO;
use App\Entity\Tenant\QrVisualConfig;
use App\Helper\DownloadHelper;
use App\Repository\QrRepository;
use App\Repository\QrVisualConfigRepository;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Margin\Margin;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class QrCodePreviewService
{
    // Values correspond to default select values in form.
    private const array ERROR_CORRECTION_LEVELS = [
        ErrorCorrectionLevel::Low->value => ErrorCorrectionLevel::Low,
        ErrorCorrectionLevel::Medium->value => ErrorCorrectionLevel::Medium,
        ErrorCorrectionLevel::Quartile->value => ErrorCorrectionLevel::Quartile,
        ErrorCorrectionLevel::High->value => ErrorCorrectionLevel::High,
    ];

    public function __construct(
        private readonly DownloadHelper $downloadHelper,
        private readonly QrVisualConfigRepository $qrVisualConfigRepository,
        private readonly QrRepository $qrRepository,
        private string $uploadPathConfig,
    ) {
    }

    /**
     * Generates QR code data using provided files, selected QR codes, and download settings.
     *
     * @param array $files            array of files uploaded in the request
     * @param array $selectedQrCodes  array of QR codes selected for generation
     * @param array $downloadSettings configuration data for QR generation
     *
     * @return array returns generated QR code data based on the provided settings
     */
    public function generateQrCode(array $files, array $selectedQrCodes, array $downloadSettings): array
    {
        /*
       Extract logo from the request (When a new image is uploaded either from batch download page or design config page.
       ImageField places uploaded files in ['logo']['file']
       FileType places uploaded files in ['logo']
       */
        $logo = null;

        if ($files && isset($files['logo'])) {
            if ($files['logo'] instanceof UploadedFile) {
                $logo = $files['logo'];
            } elseif (isset($files['logo']['file']) && $files['logo']['file'] instanceof UploadedFile) {
                $logo = $files['logo']['file'];
            }
        }

        // If a design is edited, it contains an id from where we can grab the entity.
        $entity = isset($downloadSettings['id']) && !$logo ? $this->qrVisualConfigRepository->findOneBy(['id' => $downloadSettings['id']]) : null;

        // Uploaded logo > logo from entity > logo from logoPath (only on batch download page)
        if ($entity && $entity->getLogo()) {
            $downloadSettings['logoPath'] = $this->getLogoPath($entity);
            $logo = $downloadSettings['logoPath'];
        } elseif (isset($downloadSettings['logoPath']) && !$logo instanceof UploadedFile) {
            $logo = $downloadSettings['logoPath'];
        }

        $backgroundColor = $downloadSettings['backgroundColor'];
        $foregroundColor = $downloadSettings['foregroundColor'];
        $labelSize = $downloadSettings['labelSize'];
        $labelTextColor = $downloadSettings['labelTextColor'];
        $labelMargin = [$downloadSettings['labelMarginTop'], 0, $downloadSettings['labelMarginBottom'], 0];
        $errorCorrectionLevel = self::ERROR_CORRECTION_LEVELS[$downloadSettings['errorCorrectionLevel']];

        // Define DTO
        $downloadSettingsDTO = new DownloadSettingsDTO(
            $downloadSettings['size'],
            $downloadSettings['margin'],
            $this->downloadHelper->createColorFromHex($backgroundColor),
            $this->downloadHelper->createColorFromHex($foregroundColor),
            $downloadSettings['labelText'],
            $this->downloadHelper->createFontInterface($labelSize),
            $this->downloadHelper->createColorFromHex($labelTextColor),
            new Margin(...$labelMargin),
            $errorCorrectionLevel,
        );

        return $this->generateQrCodeData($selectedQrCodes, $downloadSettingsDTO, $logo);
    }

    /**
     * Generates QR Code data for a list of selected QR Code IDs, returning the QR codes
     * as base64-encoded PNG images in a JSON response.
     *
     * @param array               $selectedQrCodes     list of QR code entity IDs to process
     * @param DownloadSettingsDTO $downloadSettingsDTO data transfer object containing settings for QR code generation
     * @param string|null         $logo                optional path to a logo image to include in the QR code
     *
     * @return array JSON response containing an array of QR codes as base64-encoded PNG images
     *
     * @throws \Exception if no QR codes are found or processed
     */
    private function generateQrCodeData(array $selectedQrCodes, DownloadSettingsDTO $downloadSettingsDTO, ?string $logo = null): array
    {
        $data = [];
        // Loop through each selected QR code entity ID
        foreach ($selectedQrCodes as $qrCodeId) {
            // Replace this with logic to retrieve the URL (or string) for each QR code entity
            $qrCodeUrl = 'examplePreview' === $qrCodeId ? 'qr visual config example preview' : $this->qrRepository->findOneBy(['id' => $qrCodeId])->getUrls()->first()->getUrl();
            $qrCodeTitle = 'examplePreview' === $qrCodeId ? 'examplePreview' : $this->qrRepository->findOneBy(['id' => $qrCodeId])->getTitle();

            if (!$qrCodeUrl) {
                continue;
            }

            // Generate the QR Code using Endroid QR Code Builder
            $builder = new Builder();
            $result = $builder->build(
                data: $qrCodeUrl,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: $downloadSettingsDTO->errorCorrectionLevel,
                size: $downloadSettingsDTO->size,
                margin: $downloadSettingsDTO->margin,
                foregroundColor: $downloadSettingsDTO->foregroundColor,
                backgroundColor: $downloadSettingsDTO->backgroundColor,
                labelText: $downloadSettingsDTO->labelText,
                labelFont: $downloadSettingsDTO->labelFont,
                labelAlignment: LabelAlignment::Center,
                labelMargin: $downloadSettingsDTO->labelMargin,
                labelTextColor: $downloadSettingsDTO->labelTextColor,
                logoPath: $logo,
                logoPunchoutBackground: false,
            );

            // Convert the QR code image to base64 and add to the array
            $data[$qrCodeTitle] = 'data:image/png;base64,'.base64_encode($result->getString());
        }

        return $data;
    }

    public function getLogoPath(QrVisualConfig $qrVisualConfig): ?string
    {
        if (!$qrVisualConfig->getLogo()) {
            return null;
        }

        return $this->uploadPathConfig.$qrVisualConfig->getLogo();
    }
}
