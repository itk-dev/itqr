<?php


namespace App\Helper;

use App\Entity\Tenant\Qr;
use App\Enum\QrStatusEnum;
use Doctrine\ORM\EntityManagerInterface;

class QrHelper
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    )
    {
    }

    /**
     * Change the status of a QR entity.
     * @throws \Exception
     */
    public function archive(Qr $qrEntity): Qr
    {
        $qrEntity->setStatus(QrStatusEnum::ARCHIVED);
        $this->entityManager->flush();

        return $qrEntity;
    }
}
