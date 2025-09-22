<?php

namespace App\Controller\Admin;

use App\Form\Type\QrArchiveType;
use App\Helper\QrHelper;
use App\Repository\QrRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class QrArchiveController extends AbstractController
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
    #[Route('/admin/archive', name: 'admin_qr_archive')]
    public function archive(int $id): Response
    {
        $form = $this->createForm(QrArchiveType::class);
        $request = $this->requestStack->getCurrentRequest();

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

            return $this->redirectToRoute('qr_index', [
                'filters' => [
                    'status' => [
                        'comparison' => '=',
                        'value' => 'ACTIVE',
                    ],
                ],
            ]);
        }

        return $this->render('form/qrArchive.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/admin/unarchive', name: 'admin_qr_unarchive')]
    public function unarchive(int $id): Response
    {
        $qrEntity = $this->qrRepository->find($id);
        if (!$qrEntity) {
            throw $this->createNotFoundException('No QR code found');
        }
        $response = $this->qrHelper->unarchive($qrEntity);

        $message = json_decode($response->getContent(), true)['message'];
        $this->addFlash('success', $message);

        return $this->redirectToRoute('qr_index', [
            'filters' => [
                'status' => [
                    'comparison' => '=',
                    'value' => 'ACTIVE',
                ],
            ],
        ]);
    }
}
