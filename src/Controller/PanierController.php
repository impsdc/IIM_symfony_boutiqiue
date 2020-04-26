<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use DateTime;

use App\Entity\Panier;
use App\Repository\PanierRepository;
use App\Form\PanierFormType;

use App\Repository\ContenuPanierRepository;



class PanierController extends AbstractController
{
    /**
     * @Route("/commande/{id}", name="commande_edit")
     */
    public function post(PanierRepository $panierRepo, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $Currentpanier = $panierRepo->findOneBy([
            'id' => $id
        ]);
        if (!$Currentpanier) {
            $this->addFlash("danger", "il y a eu un erreur");
        }

        $Currentpanier->setDate(new DateTime('now'));
        $Currentpanier->setEtat(true);
        $em->flush();


        //Reinitalize a current basket 
        $Newpanier = new Panier;
        $Newpanier->setUser($this->getUser());
        $Newpanier->setEtat(false);
        $em->persist($Newpanier);
        $em->flush();

        $this->addFlash("success", "Votre commande a été validé");

        return $this->redirectToRoute('commande');


        // target all basket bought
        $allPaniers = $panierRepo->findBy([
            'user' => $this->getUser(),
            'etat' => 1
        ]);
    }

    /**
     *  @Route("/commande/", name="commande")
     */
    public function index(PanierRepository $panier)
    {
        // target all basket bought
        $allPaniers = $panier->findBy([
            'user' => $this->getUser(),
            'etat' => 1
        ]);
        return $this->render('panier/index.html.twig', [
            'commandes' => $allPaniers,
            // 'produits' => $contenuPanier
        ]);
    }
}
