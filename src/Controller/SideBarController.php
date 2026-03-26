<?php
namespace App\Controller;

use App\Repository\LigneCommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class SideBarController extends AbstractController
{
    public function topVentes(LigneCommandeRepository $repo): Response
    {
        $top = $repo->findTopVentes(3);

        return $this->render('sidebar/top_ventes.html.twig', [
            'top' => $top
        ]);
    }
}
