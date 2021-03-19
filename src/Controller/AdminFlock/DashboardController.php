<?php

namespace App\Controller\AdminFlock;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    /**
     * Matches /
     *
     * @Route("/", name="app_homepage")
     */
    public function index()
    {
        return $this->render('admin_flock/Dashboard/homepage.html.twig', [
            'dashboard' => []
        ]);
    }


    /**
     * Matches /
     *
     * @Route("/dashboard", name="app_dashboard")
     */
    public function dashboard()
    {
        return $this->render('admin_flock/Dashboard/index.html.twig');
    }
}
