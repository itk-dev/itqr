<?php

namespace App\Helper;

use App\Entity\Tenant\Qr;
use App\Enum\QrStatusEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

class QrHelper
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TranslatorInterface $translator,
    ) {
    }

    /**
     * Change the status of a QR entity.
     *
     * @throws \Exception
     */
    public function archive(Qr $qrEntity, ?string $alternativeUrl): Response
    {
        $qrTitle = $qrEntity->getTitle();
        if ($alternativeUrl) {
            $qrEntity->setAlternativeUrl($alternativeUrl);
        }
        $qrEntity->setStatus(QrStatusEnum::ARCHIVED);
        $this->entityManager->flush();

        $message = new TranslatableMessage(
            'qr.archive.success',
            [
                '%title%' => $qrTitle,
                '%url%' => $alternativeUrl ? sprintf(' med alternativ URL: %s', $alternativeUrl) : '',
            ],
            'messages'
        );

        return new JsonResponse([
            'message' => $message->trans($this->translator),
        ]);
    }
}
