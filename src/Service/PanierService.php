<?php
namespace App\Service;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Usager;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

// Service pour manipuler le panier et le stocker en session
class PanierService
{
    ////////////////////////////////////////////////////////////////////////////
    private $session;   // Le service session
    private $boutique;  // Le service boutique
    private $panier;// Tableau associatif, la clé est un idProduit, la valeur associée est une quantité
                        //   donc $this->panier[$idProduit] = quantité du produit dont l'id = $idProduit
    private ProduitRepository $produitRepository;
    private EntityManagerInterface $em;
    const PANIER_SESSION = 'panier'; // Le nom de la variable de session pour faire persister $this->panier

    // Constructeur du service
    public function __construct(RequestStack $requestStack, ProduitRepository $produitRepository, EntityManagerInterface $em)
    {

        $this->session = $requestStack->getSession();
        $this->panier = $this->session->get(self::PANIER_SESSION, []);
        $this->produitRepository = $produitRepository;
        $this->em = $em;
    }

    // Renvoie le montant total du panier
    public function getTotal() : float
    {
      $somme = 0;
      foreach ($this->panier as $idProduit => $quantite) {
          $produit = $this->produitRepository->find($idProduit);
          if ($produit === null) {
              continue;
          }
          $somme += $produit->getPrix() * $quantite;
      }
      return $somme;
    }

    // Renvoie le nombre de produits dans le panier
    public function getNombreProduits() : int
    {
      $nbProduits = 0;
      foreach ($this->panier as $quantite) {
          $nbProduits += $quantite;
      }
      return $nbProduits;
    }

    // Ajouter au panier le produit $idProduit en quantite $quantite
    public function ajouterProduit(int $idProduit, int $quantite = 1) : void
    {
      if (isset($this->panier[$idProduit])) {
          $this->panier[$idProduit] += $quantite;
      } else {
          $this->panier[$idProduit] = $quantite;
      }
      $this->session->set(self::PANIER_SESSION, $this->panier);
    }

    // Enlever du panier le produit $idProduit en quantite $quantite
    public function enleverProduit(int $idProduit, int $quantite = 1) : void
    {
        if (!isset($this->panier[$idProduit])) {
            return;
        }
        $this->panier[$idProduit] -= $quantite;
        if ($this->panier[$idProduit] <= 0) {
            unset($this->panier[$idProduit]);
        }
        $this->session->set(self::PANIER_SESSION, $this->panier);
    }

    // Supprimer le produit $idProduit du panier
    public function supprimerProduit(int $idProduit) : void
    {
        unset($this->panier[$idProduit]);
        $this->session->set(self::PANIER_SESSION, $this->panier);
    }

    // Vider complètement le panier
    public function vider() : void
    {
      $this->panier = [];
      $this->session->set(self::PANIER_SESSION, $this->panier);
    }

    // Renvoie le contenu du panier dans le but de l'afficher
    //   => un tableau d'éléments [ "produit" => un objet produit, "quantite" => sa quantite ]
    public function getContenu(): array
    {
        $contenu = [];
        foreach ($this->panier as $idProduit => $quantite) {
            $produit = $this->produitRepository->find($idProduit);
            if ($produit === null) {
                continue;
            }
            $contenu[] = [
                "produit" => $produit,
                "quantite" => $quantite
            ];
        }
        return $contenu;
    }

    public function getNbProduits(): int
    {
        $nb = 0;

        foreach ($this->panier as $quantite) {
            $nb += $quantite;
        }

        return $nb;
    }


    public function panierToCommande(Usager $usager) : ?Commande {
        $commande = $this->getContenu();

        if (empty($commande)) {
            return null;
        }

        $commande = new Commande();
        $commande->setUsager($usager);
        $commande->setDateCreation(new \DateTime());
        $commande->setValidation(false);

         $this->em->persist($commande);
        foreach ($commande as $item) {
            $ligneCommande = new LigneCommande();
            $ligneCommande->setCommande($commande);
            $ligneCommande->setProduit($item["produit"]);
            $ligneCommande->setQuantite($item["quantite"]);
            $ligneCommande->setPrix($item["prix"]);
            $this->em->persist($ligneCommande);
        }
        $this->em->flush();
        $this->vider();
        return $commande;

    }
}
