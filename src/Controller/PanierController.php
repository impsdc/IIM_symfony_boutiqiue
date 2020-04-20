<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use DateTime;

use App\Entity\Panier;
use App\Repository\PanierRepository;
use App\Form\PanierFormType;

use App\Repository\ContenuPanierRepository;

use Symfony\Component\HttpFoundation\Request;

class PanierController extends AbstractController
{
    /**
     * @Route("/commande", name="commande")
     */
    public function index(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $panier = new Panier;
        $form = $this->createForm(PanierFormType::class, $panier);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $panier->setUser($this->getUser());
            $panier->setDate(new DateTime('now'));
            $panier->setEtat(true);

            $entityManager->persist($panier);
            $entityManager->flush();

            return $this->redirectToRoute('commande');

        }

        $allPaniers = $entityManager->getRepository(ContenuPanierRepository::class)->findAll();
        return $this->render('panier/index.html.twig', [
            'commandes' => $allPaniers,
        ]);
    }
}
