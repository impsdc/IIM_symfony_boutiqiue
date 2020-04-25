<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\PanierRepository;
use App\Repository\ContenuPanierRepository;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     *  @Route("/super/commande", name="commande_nonPayer")
     */
    public function index(PanierRepository $panier)
    {
        // target all basket bought
        $allCommandes = $panier->findBy([
            'etat' => 0
        ]);

        return $this->render('admin/index.html.twig', [
            'commandes' => $allCommandes,
        ]);
    }

    /**
     * @Route("/super/user", name="seeUser")
     */
    public function indexUser(UserRepository $userRepo)
    {
        return $this->render('admin/user.html.twig', [
            'users' => $userRepo->findby([], ["id" => "ASC"]),
        ]);
    }
}
