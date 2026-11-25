<?php

namespace App\Controller\Admin;

use App\Form\Type\BatchDownloadType;
use App\Helper\DownloadHelper;
use App\Repository\QrRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
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
    public function batchDownload(): Response
    {
        $request = $this->requestStack->getCurrentRequest();
        // EasyAdmin does not copy 'routeParams' from the query string to the controller arguments.
        // A single item from the crud context menu can be a string, thus the array cast.
        $selectedEntityIds = (array) ($request->query->all(EA::ROUTE_PARAMS)['selectedEntityIds'] ?? []);
        $form = $this->createForm(BatchDownloadType::class);

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
            'selectedQrCodes' => json_encode($selectedEntityIds, \JSON_THROW_ON_ERROR),
            'count' => count($selectedEntityIds),
        ]);
    }
}
