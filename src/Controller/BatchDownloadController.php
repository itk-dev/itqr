<?php

namespace App\Controller;

use App\Form\Type\BatchDownloadType;
use App\Helper\DownloadHelper;
use App\Repository\QrRepository;
use Endroid\QrCode\Exception\ValidationException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BatchDownloadController extends FrontPageController
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly QrRepository $qrRepository,
        private readonly DownloadHelper $downloadHelper,
    ) {
    }

    /**
     * @throws ValidationException
*@todo add permission check here.
     */
    #[Route('/batch/download', name: 'app_batch_download')]
    public function index(): Response
    {
        $form = $this->createForm(BatchDownloadType::class);

        $request = $this->requestStack->getCurrentRequest();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $logoFile = $form->get('logo')->getData();

            $qrEntities = $this->qrRepository->findBy(['id' => $request->query->all()]);

            if (!$qrEntities) {
                throw $this->createNotFoundException('No QR codes found');
            }

            return $this->downloadHelper->generateQrCodes($qrEntities, $data);
        }

        return $this->render('form/batchDownload.html.twig', [
            'form' => $form,
            'count' => count($request->query->all()),
        ]);
    }
}
