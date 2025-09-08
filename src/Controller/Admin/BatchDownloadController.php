<?php

namespace App\Controller\Admin;

use App\Form\Type\BatchDownloadType;
use App\Helper\DownloadHelper;
use App\Repository\QrRepository;
use GuzzleHttp\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BatchDownloadController extends AbstractController
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly QrRepository $qrRepository,
        private readonly DownloadHelper $downloadHelper,
    ) {
    }

    /**
     * @throws \Endroid\QrCode\Exception\ValidationException
     */
    #[Route('/admin/batch/download', name: 'admin_batch_download')]
    public function batchDownload(string|array $selectedEntityIds): Response
    {
        /*
            If this method is called from the crud context menu and only regards a single item,
            selectedEntityIds is a string. Convert to array to compatibilize.
        */
        if (!is_array($selectedEntityIds)) {
            $selectedEntityIds = [$selectedEntityIds];
        }
        $form = $this->createForm(BatchDownloadType::class);
        $request = $this->requestStack->getCurrentRequest();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $qrEntities = $this->qrRepository->findBy(['id' => $selectedEntityIds]);

            if (!$qrEntities) {
                throw $this->createNotFoundException('No QR codes found');
            }

            return $this->downloadHelper->generateQrCodes($qrEntities, (array) $data);
        }

        return $this->render('form/batchDownload.html.twig', [
            'form' => $form,
            'selectedQrCodes' => Utils::jsonEncode($selectedEntityIds),
            'count' => count($selectedEntityIds),
        ]);
    }
}
