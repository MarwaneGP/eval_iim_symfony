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

        return $this->render('admin/index.html.twig', [
            'message' => 'Points are being added to all active users.',
        ]);


    }
    #[Route('/admin/products/mine', name: 'admin_my_products')]
    public function myProducts(ProductRepository $repo, Security $security): Response
    {
        $products = $repo->findBy(['createdBy' => $security->getUser()]);
        return $this->render('admin/my_products.html.twig', ['products' => $products]);
    }
    #[Route('/admin/users', name: 'admin_user_list')]
    public function listUsers(UserRepository $repo): Response
    {
        $users = $repo->findAll();

        return $this->render('admin/user_list.html.twig', [
            'users' => $users,
        ]);
    }


}
