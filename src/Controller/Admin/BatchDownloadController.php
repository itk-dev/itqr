<?php

namespace App\Controller\Admin;

use App\Form\Type\BatchDownloadType;
use App\Helper\DownloadHelper;
use App\Repository\QrRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use function GuzzleHttp\json_encode;

final class BatchDownloadController extends DashboardController
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
    public function index(): Response
    {
        $form = $this->createForm(BatchDownloadType::class);
        $request = $this->requestStack->getCurrentRequest();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $qrEntities = $this->qrRepository->findBy(['id' => $request->query->all()]);

            if (!$qrEntities) {
                throw $this->createNotFoundException('No QR codes found');
            }

            return $this->downloadHelper->generateQrCodes($qrEntities, (array) $data);
        }

        return $this->render('form/batchDownload.html.twig', [
            'form' => $form,
            'selectedQrCodes' => json_encode($request->query->all()),
            'count' => count($request->query->all()),
        ]);
    }
}
