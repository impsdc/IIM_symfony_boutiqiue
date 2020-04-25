<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\ContenuPanier;
use App\Repository\ContenuPanierRepository;

use App\Entity\Panier;
use App\Repository\PanierRepository;
use App\Form\PanierFormType;

class ContenuPanierController extends AbstractController
{
    /**
     * @Route("/panier/", name="panier")
     */ 
    public function index(PanierRepository $panierRipo, ContenuPanierRepository $contPanier, Request $request)
    {
        $currentPanier = $panierRipo->findOneBy([
            'user' => $this->getUser(),
            'etat' => 0
        ]);
    

        $contenu =  $contPanier->findBy([
            'panier' => $currentPanier->getId()
        ]);

        $form = $this->createForm(PanierFormType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            // handle the form in Panier controller
            return $this->redirectToRoute("commande_edit", [
                'id' => $currentPanier->getId()
            ]);
        }

        return $this->render('contenu_panier/index.html.twig', [
            'paniers' => $contenu,
            "curent" => $currentPanier,
            'countPanier' => count($contenu),
            'form' => $form->createView(),
        ]);
    }
    
    

    /**
     * @Route("/panier/{id}", name="panier_delete", methods={"DELETE", "POST"})
     */ 
    public function delete($panier, Request $request, $id)
    {
        if ($this->isCsrfTokenValid('delete'.$panier->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($panier);
            $entityManager->flush();
        }
        return $this->redirectToRoute('panier', [
            "id" => $panier->getId()
        ]);
    }
}
