<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ShortUrlRedirectController extends AbstractController
{
    #[Route('/redirect/{uri}', name: 'app_short_url_redirect')]
    public function index(): RedirectResponse
    {
      return $this->redirect('http://symfony.com/doc');
    }
}
