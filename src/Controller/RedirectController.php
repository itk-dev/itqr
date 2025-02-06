<?php

namespace App\Controller;

use App\Entity\Qr;
use App\Repository\QrRepository;
use App\Repository\UrlRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

final class RedirectController extends AbstractController
{
    public function __construct(
        private readonly QrRepository $qrRepository,
    ) {
    }

    #[Route('/redirect/{uuid}', name: 'app_redirect')]
    public function index(string $uuid, UrlRepository $urlRepository): Response
    {
        // Find the QR entity by UUID
        $qr = $this->qrRepository->findOneBy(['uuid' => $uuid]);

        if (!$qr) {
            throw $this->createNotFoundException('QR code not found');
        }

        // Retrieve URLs associated with the QR entity
        $urls = $urlRepository->findBy(['qr' => $qr->getId()]);

        // @TODO: Add what happens if a qr has multiple urls with certain modes.

        if (!$urls) {
            throw $this->createNotFoundException('No URLs found for the given QR code');
        }

        // Redirect to the first URL
        return new RedirectResponse((string) $urls[0]);
    }
}
