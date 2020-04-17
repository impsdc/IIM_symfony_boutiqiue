<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//to upload product in basket
use App\Entity\ContenuPanier;
use App\Repository\ContenuPanierRepository;
use App\Form\ContenuPanierFormType;

//to upload product in basket
use App\Entity\Panier;
use App\Repository\PanierRepository;
use App\Form\PanierFormType;

use DateTime;

// to remove file
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
//for translation 
use Symfony\Contracts\Translation\TranslatorInterface;

class ProduitController extends AbstractController
{
    /**
     * @Route("/", name="home", methods={"GET"})
     */
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    /**
     * @Route("/produit/new", name="produit_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
                //le form a été envoyé, on sauvegarde l'image
                $image = $form->get('photo')->getData();
                if($image){
                    
                    $nomImage = uniqid() . '.' . $image->guessExtension();
    
                    try{
                        $image->move(
                            $this->getParameter('upload_dir'), 
                            $nomImage
                        );
                    }
                    catch(FileException $e){
                        $this->addFlash("danger","Il y a eu une erreur avec votre image"
                        );
                        return $this->redirectToRoute('home');
                    }
                    $produit->setPhoto($nomImage);
                }
            $entityManager->persist($produit);
            $entityManager->flush();

            $this->addFlash("success", "produit ajoutée");
            return $this->redirectToRoute('home');
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/produit/{id}", name="produit_show", methods={"GET", "POST"})
     */
    public function show(Produit $produit, Request $request, Panier $panier): Response
    {
        $contenuPanier = new ContenuPanier();
        $form = $this->createForm(ContenuPanierFormType::class, $contenuPanier);
        $form->handleRequest($request);
        $produitId = $produit->getId();
        $date = new DateTime('now');
        

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $contenuPanier->setDateTime($date);
            $contenuPanier->addProduit($produit);
            if($produit->getStock() < $contenuPanier->getQuantity()){
                $this->addFlash("danger", "la quantity demandé est pas disponible");
            }
            else{

                $entityManager->persist($contenuPanier);
                $entityManager->flush();
                $this->addFlash("success", "produit ajoué au panier");
            }
        
            return $this->redirectToRoute("produit_show", [
                "id" => $produitId
            ]);
        }

        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("produit/{id}/edit", name="produit_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Produit $produit): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('produit_index');
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("produit/{id}", name="produit_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Produit $produit): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('produit_index');
    }
}
