<?php

namespace App\Controller\Admin;

use App\Entity\Tenant\Qr;
use App\Entity\Tenant\QrVisualConfig;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Translation\TranslatableMessage;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // Redirect to the qr crud page until dashboad functionality is implemented
        return $this->redirectToRoute('qr_index', [
            'filters' => [
                'status' => [
                    'comparison' => '=',
                    'value' => 'ACTIVE',
                ],
            ],
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle($this->render('dashboardTitle.html.twig', [
                'logoPath' => $_ENV['APP_LOGO_PATH'],
            ])->getContent())
            ->setFaviconPath('favicon.svg')
            ->renderContentMaximized()
            ->disableDarkMode()
            ->setLocales([
                'da' => 'Dansk',
                'en' => 'English',
            ]);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud(new TranslatableMessage('menu.qr'), 'fa fa-qrcode', Qr::class)
            ->setQueryParameter('filters[status][comparison]', '=')
            ->setQueryParameter('filters[status][value]', 'ACTIVE');
        yield MenuItem::linkToCrud(new TranslatableMessage('menu.designs'), 'fa fa-palette', QrVisualConfig::class);
    }
}
