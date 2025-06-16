<?php

namespace App\Controller;

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
        private readonly QrService $qrService,
    ) {
    }

    #[Route('/qr/{uuid}', name: 'app_qr_index')]
    public function index(string $uuid): Response
    {
        // Find the QR entity by UUID
        $qr = $this->qrRepository->findOneBy(['uuid' => UuidV7::fromString($uuid)]);

        if (!$qr) {
            throw $this->createNotFoundException('QR code not found');
        }

        try {
            $data = $this->qrService->handleQrResponse($qr);

            // If we have a URL, it's a default mode QR code
            if (isset($data['url'])) {
                return new RedirectResponse($data['url']);
            }

            // Otherwise, it's a static mode QR code
            return $this->render('static.html.twig', $data);
        } catch (\RuntimeException $e) {
            throw $this->createNotFoundException($e->getMessage());
        }
    }
}
