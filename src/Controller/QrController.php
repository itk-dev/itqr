<?php

namespace App\Controller;

use App\Entity\QrHitTracker;
use App\Enum\QrModeEnum;
use App\Repository\QrRepository;
use App\Repository\UrlRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function index(string $uuid, UrlRepository $urlRepository, EntityManagerInterface $entityManager): Response
    {
        // Find the QR entity by UUID
        $uuid = UuidV7::fromString($uuid);
        $qr = $this->qrRepository->findOneBy(['uuid' => $uuid]);

        if (!$qr) {
            throw $this->createNotFoundException('QR code not found');
        }

        // Create QR hit tracker entry
        $qrHitTracker = new QrHitTracker();
        $qrHitTracker->setQr($qr);
        $qrHitTracker->setTimestamp(new \DateTimeImmutable());
        $entityManager->persist($qrHitTracker);
        $entityManager->flush();

        $urls = $qr->getUrls();

        if (QrModeEnum::DEFAULT === $qr->getMode()) {
            if ($urls->isEmpty()) {
                throw $this->createNotFoundException('No URLs found for the given QR code');
            }

            return new RedirectResponse((string) $urls[0]);
        }

        if (QrModeEnum::STATIC === $qr->getMode()) {
            return $this->render('static.html.twig', [
                'title' => $qr->getTitle(),
                'description' => $qr->getDescription(),
            ]);
        }

        throw $this->createNotFoundException('Invalid QR mode');
    }
}
