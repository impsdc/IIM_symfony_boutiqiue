<?php

namespace App\Controller;

use App\Entity\User;

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

use App\Repository\PanierRepository;

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
            if ($image) {

                $nomImage = uniqid() . '.' . $image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('upload_dir'),
                        $nomImage
                    );
                } catch (FileException $e) {
                    $this->addFlash(
                        "danger",
                        "Il y a eu une erreur avec votre image"
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
    public function show(Produit $produit, Request $request, $id, PanierRepository $panierRepo): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $contenuPanier = new ContenuPanier();
        $form = $this->createForm(ContenuPanierFormType::class, $contenuPanier);
        $form->handleRequest($request);

        $contenuPanier->setProduit($produit);
        $contenuPanier->setDatetime(new DateTime('now'));

        //target the current Panier
        $currentPanier = $panierRepo->findOneBy([
            'user' => $this->getUser(),
            'etat'   => 0
        ]);

        $contenuPanier->setPanier($currentPanier);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($produit->getStock() > $contenuPanier->getQuantity()) { // check if quantity wanted is available

                $oldContenuPanier = $entityManager->getRepository(ContenuPanier::class)->findOneBy([
                    "produit" => $id,
                    "panier" => $currentPanier
                ]);
                if ($oldContenuPanier) { //check if he had already add this product 
                    //if yes add up the quantity
                    $quantityIni = $oldContenuPanier->getQuantity();
                    $newQuantity = $quantityIni + $contenuPanier->getQuantity();
                    $contenuPanier->setQuantity($newQuantity);

                    // //remove the older one
                    // $entityManager->remove($oldContenuPanier);

                    $entityManager->persist($contenuPanier);
                    $entityManager->flush();
                    $this->addFlash("success", "La quantité désiré a été mise a jour");
                } else {
                    //if not just submit
                    $entityManager->persist($contenuPanier);
                    $entityManager->flush();
                    $this->addFlash("success", "produit ajoué au panier");
                }
            } else {
                $this->addFlash("danger", "la quantity demandé est pas disponible");
            }
            return $this->redirectToRoute("produit_show", [
                "id" => $id
            ]);
        }
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/produit/{id}/edit", name="produit_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Produit $produit): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash("success", "le produit a été modifier");
            return $this->redirectToRoute('home');
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/produit/{id}", name="produit_delete")
     */
    public function delete(Produit $produit = null)
    {
        if ($produit != null) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($produit);
            $entityManager->flush();
            $this->addFlash("success", "le produit a été supprimer");
            return $this->redirectToRoute('home');
        } else {
            $this->addFlash("danger", "le produit ciblé n'existe pas");
            return $this->redirectToRoute('home');
        }
    }
}
