<?php

namespace App\Controller;

use App\Enum\QrModeEnum;
use App\Repository\QrRepository;
use App\Service\QrService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\UuidV7;

final class QrController extends AbstractController
{
    public function __construct(
        private readonly QrRepository $qrRepository,
        private readonly QrService $QrService,
    ) {
    }

    #[Route('/qr/{uuid}', name: 'app_qr_index')]
    public function index(string $uuid): Response
    {
        // Find the QR entity by UUID
        $uuid = UuidV7::fromString($uuid);
        $qr = $this->qrRepository->findOneBy(['uuid' => $uuid]);

        if (!$qr) {
            throw $this->createNotFoundException('QR code not found');
        }

        // Count qr code hit.
        $this->QrService->qrHitTrackerCount($qr);

        // Default qr codes get redirected to the destination url.
        if (QrModeEnum::DEFAULT === $qr->getMode()) {
            $urls = $qr->getUrls();

            if ($urls->isEmpty()) {
                throw $this->createNotFoundException('No URLs found for the given QR code');
            }

            return new RedirectResponse((string) $urls[0]);
        }

        // Static qr codes get rendered via static template.
        if (QrModeEnum::STATIC === $qr->getMode()) {
            return $this->render('static.html.twig', [
                'title' => $qr->getTitle(),
                'description' => $qr->getDescription(),
            ]);
        }

        // If no modes match, something is wrong.
        throw $this->createNotFoundException('Invalid QR mode');
    }
}
