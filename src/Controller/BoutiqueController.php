<?php

namespace App\Controller;

use App\Service\BoutiqueService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BoutiqueController extends AbstractController
{
    #[Route(
        path:'boutique',
        name: 'app_boutique_index',
    )]
    public function index(BoutiqueService $boutique): Response
    {
        $categories = $boutique->findAllCategories();
        return $this->render('boutique/index.html.twig', [
            'controller_name' => 'BoutiqueController',
            'categories' => $categories,
        ]);
    }


    #[Route(
        path:'boutique/rayon/{idCategorie}',
        name: 'app_boutique_rayon',
    )]
    public function rayon(int $idCategorie): Response
    {
        return $this->render('boutique/rayon.html.twig', [
            'categories' => $idCategorie,
        ]);
    }
}
