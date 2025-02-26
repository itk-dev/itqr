<?php

namespace App\Controller;

use App\Repository\QrRepository;
use App\Repository\UrlRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\UuidV7;

final class QrController extends AbstractController
{
    public function __construct(
        private readonly QrRepository $qrRepository,
    ) {
    }

    #[Route('/qr/{uuid}', name: 'app_qr_index')]
    public function index(string $uuid, UrlRepository $urlRepository): Response
    {
        // Find the QR entity by UUID
        $uuid = UuidV7::fromString($uuid);
        $qr = $this->qrRepository->findOneBy(['uuid' => $uuid]);

        if (!$qr) {
            throw $this->createNotFoundException('QR code not found');
        }

        $urls = $qr->getUrls();

        // @TODO: Add what happens if a qr has multiple urls with certain modes.

        if ($urls->isEmpty()) {
            throw $this->createNotFoundException('No URLs found for the given QR code');
        }

        // Redirect to the first URL
        // @TODO enable "kiosk mode" for codes with multiple urls
        return new RedirectResponse((string) $urls[0]);
    }
}
