<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): RedirectResponse
    {
        return $this->redirect('/api/docs');
    }
}


// This controller redirects the root URL to the API documentation page.
// It uses Symfony's routing and response handling to achieve this.
// The `#[Route('/', name: 'app_home')]` annotation defines the route for the home page.
// The `index` method returns a `RedirectResponse` that points to `/api/docs`, which is where the API documentation is presumably located.


