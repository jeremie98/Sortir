<?php

namespace App\Controller;

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
        // date du jour
        // ...

        return $this->render('default/home.html.twig', [
            'controller_name' => 'DefaultController',
            'participant' => $user
        ]);
    }


}
