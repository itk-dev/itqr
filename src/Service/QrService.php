<?php

namespace App\Service;

use App\Entity\QrHitTracker;
use App\Entity\Tenant\Qr;
use App\Enum\QrModeEnum;
use Doctrine\ORM\EntityManagerInterface;

readonly class QrService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return array{url: string}|array{title: string, description: string}
     *
     * @throws \RuntimeException
     */
    public function handleQrResponse(Qr $qr): array
    {
        // Count qr code hit
        $this->qrHitTrackerCount($qr);

        return match ($qr->getMode()) {
            QrModeEnum::DEFAULT => $this->getDefaultModeData($qr),
            QrModeEnum::STATIC => $this->getStaticModeData($qr),
            default => throw new \RuntimeException('Invalid QR mode'),
        };
    }

    /**
     * @return array{url: string}
     *
     * @throws \RuntimeException
     */
    private function getDefaultModeData(Qr $qr): array
    {
        $urls = $qr->getUrls();

        if ($urls->isEmpty()) {
            throw new \RuntimeException('No URLs found for the given QR code');
        }

        return ['url' => (string) $urls[0]];
    }

    /**
     * @return array{title: string, description: string}
     */
    private function getStaticModeData(Qr $qr): array
    {
        return [
            'title' => $qr->getTitle(),
            'description' => $qr->getDescription(),
        ];
    }

    private function qrHitTrackerCount(Qr $qr): void
    {
        $qrHitTracker = new QrHitTracker();
        $qrHitTracker->setQr($qr);
        $qrHitTracker->setTimestamp(new \DateTimeImmutable());
        $this->entityManager->persist($qrHitTracker);
        $this->entityManager->flush();
    }
}
