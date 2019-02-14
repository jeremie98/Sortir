<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/my_profil", name="my_profil");
     */
    public function showMyProfil() {
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $currentUser =$userRepo->find($this->getUser());


        return $this->render('user/user_profil.html.twig', [
            'currentUser' => $currentUser
        ]);
    }

    /**
     * @Route("/déconnexion", name="app_logout")
     */
    public function logout(){
    }


}
