<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Repository\ContenuPanierRepository;

use App\Entity\ContenuPanier;
use App\Repository\PanierRepository;
use App\Form\PanierFormType;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/{_locale}")
 */
class ContenuPanierController extends AbstractController
{
    /**
     * @Route("/panier", name="panier")
     */
    public function index(PanierRepository $panierRipo, ContenuPanierRepository $contPanier, Request $request)
    {
        //target current basket
        $currentPanier = $panierRipo->findOneBy([
            'user' => $this->getUser(),
            'etat' => 0
        ]);

        $form = $this->createForm(PanierFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // handle the form in Panier controller
            return $this->redirectToRoute("commande_edit", [
                'id' => $currentPanier->getId() //pass the id of the current basket
            ]);
        }

        //get all the products that match with the current basket
        $contenu =  $contPanier->findBy([
            'panier' => $currentPanier->getId()
        ]);

        return $this->render('contenu_panier/index.html.twig', [
            'paniers' => $contenu,
            'countPanier' => count($contenu),
            'form' => $form->createView(),
        ]);
    }



    /**
     * @Route("/panier/{id}", name="panier_delete")
     */
    public function delete(ContenuPanier $panier = null, PanierRepository $panierRipo, TranslatorInterface $translator)
    {
        $currentPanier = $panierRipo->findOneBy([
            'user' => $this->getUser(),
            'etat' => 0
        ]);

        if ($panier != null) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($panier);
            $entityManager->flush();

            $this->addFlash("success", $translator->trans('contentCart.success'));
            return $this->redirectToRoute('panier', [
                'id' => $currentPanier->getId()
            ]);
        } else {
            $this->addFlash("danger", $translator->trans('contentCart.danger'));
            return $this->redirectToRoute('panier', [
                'id' => $currentPanier->getId()
            ]);
        }
    }
}
