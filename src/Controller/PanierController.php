<?php

namespace App\Controller;

use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PanierController extends AbstractController
{
    #[Route(
        path: '{_locale}/panier',
        name: 'app_panier_index',
        requirements: ['_locale' => '%app.supported_locales%']
    )]
    public function index(PanierService $panier): Response
    {
        return $this->render('panier/index.html.twig', [
            'panier' => $panier->getContenu(),
            'total' => $panier->getTotal(),
        ]);
    }


    #[Route(
        path: '{_locale}/panier/ajouter/{idProduit}/{quantite}',
        name: 'app_panier_ajouter',
        requirements: ['_locale' => '%app.supported_locales%']
    )]
    public function ajouter(PanierService $panier, int $idProduit ,int $quantite): Response
    {
        $panier->ajouterProduit($idProduit, $quantite);
        return $this->redirectToRoute('app_panier_index');
    }


    #[Route(
        path: '{_locale}/panier/enlever/{idProduit}/{quantite}',
        name: 'app_panier_enlever',
        requirements: ['_locale' => '%app.supported_locales%']
    )]
    public function enlever(PanierService $panier, int $idProduit ,int $quantite): Response
    {
        $panier->enleverProduit($idProduit, $quantite);
        return $this->redirectToRoute('app_panier_index');
    }



    #[Route(
        path: '{_locale}/panier/supprimer/{idProduit}',
        name: 'app_panier_supprimer',
        requirements: ['_locale' => '%app.supported_locales%']
    )]
    public function supprimer(PanierService $panier, int $idProduit): Response
    {
        $panier->supprimerProduit($idProduit);
        return $this->redirectToRoute('app_panier_index');
    }



    #[Route(
        path: '{_locale}/panier/vider',
        name: 'app_panier_vider',
        requirements: ['_locale' => '%app.supported_locales%']
    )]
    public function vider(PanierService $panier): Response
    {
        $panier->vider();
        return $this->redirectToRoute('app_panier_index');
    }
}
