<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;

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

        // Si le visiteur est déjà identifié, on le redirige vers l'accueil
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('home');
        }

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
     * @Route("/profil/{id}",
     * name="a_profil",
     *     requirements={"id": "\d+"},
     *     methods={"GET"}
     *     )
     */
    public function showAnotherProfil(int $id){
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepository->find($id);

        return $this->render('user/other_profil.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/déconnexion", name="app_logout")
     */
    public function logout(){}






}
