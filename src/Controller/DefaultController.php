<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
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

    /**
     * @Route("/my_profil", name="my_profil"),
     * ;
     */
    public function showMyProfil() {

        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $currentUser =$userRepo->find($this->getUser());


        return $this->render('user/user_profil.html.twig', [
            'currentUser' => $currentUser
        ]);
    }

    /**
     * @Route("/update_my_profil", name="update_my_profil"),
     * ;
     */
    public function updateMyProfil(Request $request, UserPasswordEncoderInterface $passwordEncoder) {

        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $currentUser =$userRepo->find($this->getUser());

        $currentUser->setPseudo($request->request->get('pseudo'));
        $currentUser->setPrenom($request->request->get('prenom'));
        $currentUser->setNom($request->request->get('nom'));
        $currentUser->setTelephone($request->request->get('tel'));
        $password = $request->request->get('password');
        $passwordEncoded = $passwordEncoder->encodePassword($currentUser, $password);
        $currentUser->setPassword($passwordEncoded);
        $currentUser->setVille($request->request->get('ville'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($currentUser);
        $em->flush();

        return $this->redirectToRoute('home', [
            'currentUser' => $currentUser
        ]);
    }

}
