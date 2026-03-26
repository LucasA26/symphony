<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use App\Service\BoutiqueService;
use PhpParser\Node\Expr\Cast\Object_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BoutiqueController extends AbstractController
{
    #[Route(
        path:'/{_locale}/boutique',
        name: 'app_boutique_index',
        requirements: ['_locale' => '%app.supported_locales%']
    )]
    public function index(CategorieRepository $categorieRepository): Response
    {
        $categories = $categorieRepository->findAllCategories($categorieRepository);
        return $this->render('boutique/index.html.twig', [
            'controller_name' => 'BoutiqueController',
            'categories' => $categories,
        ]);
    }

    #[Route(
        path: '/{_locale}/boutique/chercher/{recherche}',
        name: 'app_boutique_chercher',
        requirements: ['_locale' => '%app.supported_locales%', 'recherche' => '.+'],
        defaults: ['recherche' => '']
    )]
    public function chercher(ProduitRepository $produitRepository, string $recherche): Response
    {
        $produits = $produitRepository->find($recherche);
        return $this->render('boutique/chercher.html.twig', [
            'produits' => $produits,
            'recherche' => $recherche,
        ]);
    }

    #[Route(
        path:'/{_locale}/boutique/rayon/{idCategorie}',
        name: 'app_boutique_rayon',
        requirements: ['_locale' => '%app.supported_locales%']
    )]
    public function rayon(CategorieRepository $categorieRepository, int $idCategorie, ProduitRepository $produitRepository): Response
    {
        $categorie = $categorieRepository->findCategorieById($categorieRepository, $idCategorie);
         $produits = $produitRepository->findBy(['categorie' => $categorie]);
        return $this->render('boutique/rayon.html.twig', [
            'categorie' => $categorie,
           'produits' => $produits,
        ]);
    }
}
