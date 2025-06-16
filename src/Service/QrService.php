<?php

namespace App\Service;

use App\Entity\QrHitTracker;
use App\Entity\Tenant\Qr;
use Doctrine\ORM\EntityManagerInterface;

class QrService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function qrHitTrackerCount(Qr $qr): void
    {
        $qrHitTracker = new QrHitTracker();
        $qrHitTracker->setQr($qr);
        $qrHitTracker->setTimestamp(new \DateTimeImmutable());
        $this->entityManager->persist($qrHitTracker);
        $this->entityManager->flush();
    }
}
