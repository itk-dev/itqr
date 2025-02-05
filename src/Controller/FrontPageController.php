<?php

namespace App\Controller;

use App\Entity\Qr;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Translation\TranslatableMessage;

class FrontPageController extends AbstractDashboardController
{
    #[Route('/', name: 'admin')]
    public function index(): Response
    {
        return $this->render('@EasyAdmin/page/content.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('ITQR')
            ->setFaviconPath('favicon.svg')
            ->renderContentMaximized()
            ->disableDarkMode()
            ->generateRelativeUrls()
            ->setLocales([
                'da' => 'Dansk',
                'en' => 'English',
            ]);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud(new TranslatableMessage('Qr code'), 'fa fa-qrcode', Qr::class);
    }
}
