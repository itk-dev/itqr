<?php

namespace App\Controller\Admin;

use App\Form\Type\QrArchiveType;
use App\Helper\QrHelper;
use App\Repository\QrRepository;
use GuzzleHttp\Utils;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class QrArchiveController extends DashboardController
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly QrRepository $qrRepository,
        private readonly QrHelper $qrHelper,
    ) {
    }

    /**
     * @throws \Exception
     */
    #[Route('/admin/archive', name: 'admin_qr_archive', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        $form = $this->createForm(QrArchiveType::class);
        $request = $this->requestStack->getCurrentRequest();
        $id = (int) $request->query->get('id');

        // Get the QR entity first
        $qrEntity = $this->qrRepository->find($id);
        if (!$qrEntity) {
            throw $this->createNotFoundException('No QR code found');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $alternativeUrl = $form->get('alternativeUrl')->getData();
            $response = $this->qrHelper->archive($qrEntity, $alternativeUrl);

            $message = json_decode($response->getContent(), true)['message'];
            $this->addFlash('success', $message);

            return $this->redirectToRoute('qr_index');
        }

        return $this->render('form/qrArchive.html.twig', [
            'form' => $form,
            'qr' => $qrEntity, // Pass the entity
            'id' => $id, // Pass the ID explicitly
            'selectedQrCodes' => Utils::jsonEncode([$id]),
            'count' => 1,
        ]);
    }
}
