<?php
namespace App\Controller;

use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommandeController extends AbstractController
{
    #[Route(
        path: '/{_locale}/usager/commande',
        name: 'app_usager_commandes',
        requirements: ['_locale' => '%app.supported_locales%']
    )]
    public function mesCommandes(CommandeRepository $cmd): Response
    {
        $usager = $this->getUser();

        $commandes = $cmd->findBy(['usager' => $usager], ['dateCreation' => 'DESC']);

        return $this->render('usager/commandes.html.twig', [
            'commandes' => $commandes
        ]);
    }

    #[Route(
        path: '/{_locale}/usager/commande/{id}',
        name: 'app_usager_commande_detail',
        requirements: ['_locale' => '%app.supported_locales%']
    )]
    public function detail(CommandeRepository $cmd, int $id): Response
    {
        $commande = $cmd->find($id);

        // Sécurité : l’usager ne peut voir QUE ses commandes
        if (!$commande || $commande->getUsager() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('usager/commande_detail.html.twig', [
            'commande' => $commande
        ]);
    }
}
