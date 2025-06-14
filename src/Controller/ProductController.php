<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use App\Entity\Produit;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    #[Route('/products', name: 'product_index')]
    public function index(ProduitRepository $repo): Response
    {
        $products = $repo->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }
    #[Route('/products/{id}', name: 'product_show')]
    public function show(Produit $produit): Response
    {
        return $this->render('product/show.html.twig', [
            'produit' => $produit,
        ]);
    }
    #[Route('/products/{id}/buy', name: 'product_buy')]
    public function buy(Produit $produit, Security $security, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $security->getUser();

        if (!$user->isActif()) {
            $this->addFlash('error', 'You are not allowed to buy products.');
            return $this->redirectToRoute('product_index');
        }

        if ($user->getPoints() < $produit->getPrix()) {
            $this->addFlash('error', 'Not enough points.');
            return $this->redirectToRoute('product_index');
        }

        $user->setPoints($user->getPoints() - $produit->getPrix());

        
        $notification = new Notification();
        $notification->setLabel(sprintf(
            'PURCHASE | %s bought %s for %d points on %s',
            $user->getEmail(),
            $produit->getNom(),
            $produit->getPrix(),
            (new \DateTime())->format('Y-m-d H:i')
        ));
        $notification->setUser($user); 

        $em->persist($notification);
        $em->flush();


        $em->flush();

        $this->addFlash('success', 'Product purchased!');
        return $this->redirectToRoute('product_index');
    }


    
}
