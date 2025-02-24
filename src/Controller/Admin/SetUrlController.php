<?php

namespace App\Controller\Admin;

use App\Entity\Tenant\Qr;
use App\Entity\Tenant\Url;
use App\Form\SetUrlType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SetUrlController extends DashboardController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private readonly AdminUrlGenerator $adminUrlGenerator,
        private readonly RequestStack $requestStack,
    ) {
    }

    /**
     * @todo add permission check here.
     */
    #[Route('/admin/batch/set_url', name: 'admin_set_url')]
    public function index(): Response
    {
        $form = $this->createForm(SetUrlType::class);

        $request = $this->requestStack->getCurrentRequest();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            foreach ($request->query->all() as $id) {
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

            // Create redirect url.
            $redirectUrl = $this->adminUrlGenerator
              ->setRoute('admin')
              ->setController(QrCrudController::class)
              ->setAction('index')
              ->generateUrl();

            return $this->redirect($redirectUrl);
        }

        return $this->render('form/setUrl.html.twig', [
            'form' => $form,
        ]);
    }
}
