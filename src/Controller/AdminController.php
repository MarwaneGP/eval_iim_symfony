<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    #[Route('/admin/add-points', name: 'admin_add_points')]
    public function addPoints(MessageBusInterface $bus): Response
    {
        $bus->dispatch(new AddPointsToActiveUsers());
        $this->addFlash('success', 'Points are being added to all active users.');

        return $this->redirectToRoute('admin_dashboard');
    }
}
