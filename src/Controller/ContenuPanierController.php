<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\ContenuPanier;
use App\Repository\ContenuPanierRepository;
use App\Form\ContenuPanierFormType;

class ContenuPanierController extends AbstractController
{
    /**
     * @Route("/panier", name="contenu_panier")
     */ 
    public function index(Request $response, ContenuPanier $panier)
    {
        
    }
}
