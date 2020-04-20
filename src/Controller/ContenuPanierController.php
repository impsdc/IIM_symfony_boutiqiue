<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\ContenuPanier;
use App\Repository\ContenuPanierRepository;
use App\Form\ContenuPanierFormType;

use App\Entity\Panier;
use App\Repository\PanierRepository;
use App\Form\PanierFormType;

class ContenuPanierController extends AbstractController
{
    /**
     * @Route("/panier", name="panier")
     */ 
    public function index(ContenuPanierRepository $panierRipo, Request $response)
    {
        $panier = new Panier;
        $form = $this->createForm(PanierFormType::class, $panier, [
            'action' => $this->generateUrl('commande'),
            'method' => 'POST'
        ]);
        
        return $this->render('contenu_panier/index.html.twig', [
            'paniers' => $panierRipo->findAll(),
            'countPanier' => count($panierRipo->findAll()),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/panier/{id}", name="panier_delete", methods={"DELETE"})
     */ 
    public function delete(ContenuPanier $panier, Request $request)
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
