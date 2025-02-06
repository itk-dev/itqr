<?php

namespace App\Controller;

use App\Entity\Qr;
use App\Enum\QrModeEnum;
use App\Repository\QrRepository;
use App\Repository\UrlRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

final class RedirectController extends AbstractController
{
    public function __construct(
        private readonly QrRepository $qrRepository,
    )
    {

    }
    #[Route('/redirect/{uuid}', name: 'app_redirect')]
    public function redirectToUrl(string $uuid, EntityManagerInterface $entityManager, UrlRepository $urlRepository): Response
    {
        // Normalize UUID by removing '0x' prefix if present
        if (str_starts_with($uuid, '0x')) {
            $uuid = substr($uuid, 2);
        }

        // Convert hexadecimal UUID to Symfony UUID object
        try {
            $uuid = Uuid::fromBinary(hex2bin($uuid));
        } catch (\Exception $e) {
            throw $this->createNotFoundException('Invalid UUID format');
        }

        // Find the QR entity by UUID
        $qr = $this->qrRepository->findOneBy(['uuid' => $uuid]);

        if (!$qr) {
            throw $this->createNotFoundException('QR code not found');
        }

        // Retrieve URLs associated with the QR entity
        $urls = $urlRepository->findBy(['qr' => $qr->getId()]);

        if (!$urls) {
            throw $this->createNotFoundException('No URLs found for the given QR code');
        }

        // Redirect to the first URL
        return new RedirectResponse((string) $urls[0]);
    }
}
