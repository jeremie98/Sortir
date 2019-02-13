<?php

namespace App\Controller;

use App\Entity\Sortie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

class DefaultController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function home()
    {
        // récupération de l'utilisateur connecté
        $user = $this->getUser();

        // récupération de toutes les sorties
        $sortieRepository = $this->getDoctrine()->getRepository(Sortie::class);
        $sorties = $sortieRepository->findAll();

        return $this->render('default/home.html.twig', [
            'controller_name' => 'DefaultController',
            'participant' => $user,
            'sorties' => $sorties
        ]);
    }


}
