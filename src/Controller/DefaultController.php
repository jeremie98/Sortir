<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class DefaultController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function home(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // récupération de l'utilisateur connecté
        $user = $this->getUser();


        /*$archiveRepository = $this->getDoctrine()->getRepository(Sortie::class);
        $archive= $archiveRepository->findSortiesNonArchivees(new \DateTime('now'));
*/
        // récupération de toutes les sorties
        $sortieRepository = $this->getDoctrine()->getRepository(Sortie::class);
        $sorties = $sortieRepository->findSortiesPlusRecentes();

        // récupération des sites pour la liste déroulante
        $siteRepository = $this->getDoctrine()->getRepository(Site::class);
        $sites = $siteRepository->findAll();


        /* GESTION DES ETATS EN FONCTION DE LA DATE */
        $dateJour = new \DateTime('now');

        foreach($sorties as $sortie)
        {
            if($sortie->getDateLimiteInscription() < $dateJour and $sortie->getDateSortie() > $dateJour)
            {
                $sortie->setEtat("Clôturée");
                $em->persist($sortie);
            }
            if($sortie->getDateSortie() < $dateJour)
            {
                $sortie->setEtat("Passée");
                $em->persist($sortie);
            }
            if($sortie->getDateSortie()->format("Y-m-d") == $dateJour->format("Y-m-d"))
            {
                $sortie->setEtat("En cours");
                $em->persist($sortie);
            }

        }
        $em->flush();


        /* Filtres sur les sorties*/

        if($request->request->get("site-select")){
            // site sélectionné
            $siteSelect = $siteRepository->find($request->request->get("site-select"));

            $sorties = $sortieRepository->findBy(
                ['siteOrg' => $siteSelect]
            );
        }
        if($request->request->get("search-bar")){
            $sorties = $sortieRepository->findSortiesContenant($request->request->get("search-bar"));
        }
        /*if($request->request->get("dateEntre") && $request->request->get("dateEt")){


            $dateEntre = new \DateTime($request->request->get("dateEntre")|date('Y-m-d H:i:s'));
            $dateEt = new \DateTime($request->request->get("dateEt")|date('Y-m-d H:i:s'));

            $sorties = $sortieRepository->findSortiesEntreDates($dateEntre, $dateEt);
        }*/

        if($request->request->get("sortOrg")){
            $sorties = $sortieRepository->findBy(
                ['organisateur' => $this->getUser()],
                ['dateSortie' => 'ASC']
            );
        }
        if($request->request->get("sortInsc")){

            $sorties = $this->getUser()->getSorties();
        }
        /*if($request->request->get("sortPasInsc")){

            $sortiesP = null;
            foreach($sorties as $sortie){
                if(in_array($user, $sorties)){
                    $sortiesP = $sortie;
                }
            }
            $sorties = $sortiesP;
        }*/
        if($request->request->get("sortPass")){
            $sortieRepository = $this->getDoctrine()->getRepository(Sortie::class);
            $sorties = $sortieRepository->findSortiesPass();
        }

        return $this->render('default/home.html.twig', [
            'controller_name' => 'DefaultController',
            'user' => $user,
            'sorties' => $sorties,
            'sites' => $sites
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
     * @Route("/photo", name="photo")
     */
    public function photo(Request $request){
        $file = $this->getUser()->getPhotoPath($request->request->get('photo'));
        dd($file);
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        $file->move(
            $this->getParameter('image_directory'),$fileName
        );

        $this->getUser()->setPhotoPath($fileName);
        $em = $this->getDoctrine()->getManager();
        $em->persist($this->getUser());
        $em->flush();

        return $this->render('ville/ville.html.twig');

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
        if(!empty($password)){
            $passwordEncoded = $passwordEncoder->encodePassword($currentUser, $password);
            $currentUser->setPassword($passwordEncoded);
        }
        $currentUser->setVille($request->request->get('ville'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($currentUser);
        $em->flush();

        return $this->redirectToRoute('home', [
            'currentUser' => $currentUser
        ]);
    }

}
