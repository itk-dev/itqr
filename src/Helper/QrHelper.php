<?php


namespace App\Helper;

use App\Entity\Tenant\Qr;
use App\Enum\QrStatusEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

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
    public function archive(Qr $qrEntity, ?string $alternativeUrl): Response
    {
        if ($alternativeUrl) {
            $qrEntity->setAlternativeUrl($alternativeUrl);
        }
        $qrEntity->setStatus(QrStatusEnum::ARCHIVED);
        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'QR code archived successfully' . ($alternativeUrl ? " with alternative URL: $alternativeUrl" : '')
        ]);
    }
}
