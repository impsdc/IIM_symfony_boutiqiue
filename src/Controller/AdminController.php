<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/commande", name="commande")
     */
    public function indexCommande()
    {

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/user", name="admin_user")
     */
    public function indexUser(UserRepository $userRepo)
    {
        return $this->render('admin/user.html.twig', [
            'users' => $userRepo->findby([], [ "id" => "ASC"]),
        ]);
    }
}
