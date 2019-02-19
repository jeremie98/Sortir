<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }


    /**
     * @Route("/register/csv", name="aaa")
     */
    public function importAction()
    {

        $utilisateurs = array(); // Tableau qui va contenir les éléments extraits du fichier CSV
        $row = 0; // Représente la ligne
        // Import du fichier CSV
        if (($handle = fopen(__DIR__ . "\public\Ressources\Utilisateurs.csv", "r")) !== FALSE) { // Lecture du fichier, à adapter
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) { // Eléments séparés par un point-virgule, à modifier si necessaire
                $num = count($data); // Nombre d'éléments sur la ligne traitée
                $row++;
                for ($c = 0; $c < $num; $c++) {
                    $utilisateurs[$row] = array(
                        "nom" => $data[0],
                        "prenom" => $data[1],
                        "pseudo" => $data[2],
                        "email" => $data[3],
                        "password" => $data[4],
                        "telephone" => $data[5],
                        "ville" => $data[6],
                        "site" => $data[7]
                    );
                }

            }
            fclose($handle);
        }


        $em = $this->getDoctrine()->getManager(); // EntityManager pour la base de données

        // Lecture du tableau contenant les utilisateurs et ajout dans la base de données



            foreach ($utilisateurs as $utilisateur) {


                //dd($utilisateurs);

                // On crée un objet utilisateur
                $user = new User();
                $user->setEmail($utilisateur["email"]);
                $user->setPassword($utilisateur["password"]);
                $user->setPseudo($utilisateur["pseudo"]);
                $user->setPrenom($utilisateur["prenom"]);
                $user->setNom($utilisateur["nom"]);
                $user->setTelephone($utilisateur["telephone"]);
                $user->setVille($utilisateur["ville"]);

                $SiteRepository = $this->getDoctrine()->getRepository(Site::class);
                $site = $SiteRepository->find($utilisateurs[2]["site"]);

                $user->setUserSite($site);


                // Encode le mot de passe
                /*    $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
                    $plainpassword = $utilisateur["password"];
                    $password = $encoder->encodePassword($plainpassword, $user->getSalt());
        */


                // Hydrate l'objet avec les informations provenants du fichier CSV


                // Enregistrement de l'objet en vu de son écriture dans la base de données
                $em->persist($user);

           }


        // Ecriture dans la base de données
        $em->flush();


        // Renvoi la réponse (ici affiche un simple OK pour l'exemple)
        return new Response('OK');
        return $this->redirectToRoute('home');
    }
}
