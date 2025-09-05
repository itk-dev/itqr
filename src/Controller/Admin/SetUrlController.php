<?php

namespace App\Controller\Admin;

use App\Entity\Tenant\Qr;
use App\Entity\Tenant\Url;
use App\Form\SetUrlType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SetUrlController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestStack           $requestStack,
    ) {
    }

    /**
     * @todo add permission check here.
     */
    #[Route('/admin/batch/set_url', name: 'admin_set_url')]
    public function batchSetUrl(array $selectedEntityIds): Response
    {
        $form = $this->createForm(SetUrlType::class);

        $request = $this->requestStack->getCurrentRequest();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            foreach ($selectedEntityIds as $id) {
                $qr = $this->entityManager->find(Qr::class, $id);

                // Remove all existing urls from the Qr code.
                $qr->removeAllUrls();

                // Create the new URL.
                $url = new Url();
                $url->setUrl($data['url']);
                $this->entityManager->persist($url);

                // Attach the url to the qr code.
                $qr->addUrl($url);
            }

            $this->entityManager->flush();

            return $this->redirectToRoute('qr_index', [
                'filters' => [
                    'status' => [
                        'comparison' => '=',
                        'value' => 'ACTIVE',
                    ],
                ],
            ]);
        }

        return $this->render('form/setUrl.html.twig', [
            'form' => $form,
            'count' => count($selectedEntityIds),
        ]);
    }
}
