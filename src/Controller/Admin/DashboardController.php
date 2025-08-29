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
        return $this->redirectToRoute('qr_index');
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
        yield MenuItem::linkToCrud(new TranslatableMessage('QR Codes'), 'fa fa-qrcode', Qr::class);
        yield MenuItem::linkToCrud(new TranslatableMessage('QR Designs'), 'fa fa-palette', QrVisualConfig::class);
    }
}
