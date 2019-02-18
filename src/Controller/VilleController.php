<?php

namespace App\Controller;

use App\Entity\Ville;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class VilleController extends AbstractController
{
    /**
     * @Route("/ville", name="ville")
     */
    public function index()
    {

        $VilleRepository = $this->getDoctrine()->getRepository(Ville::class);
        $villes = $VilleRepository->findAll();


        return $this->render('ville/ville.html.twig', [
            'controller_name' => 'VilleController',
            'villes' => $villes
        ]);
    }
}
