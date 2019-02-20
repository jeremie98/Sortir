<?php

namespace App\Controller;

use App\Entity\Annulation;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\SortieType;
use App\Form\VilleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{



    /**
     * @Route("/sortie/create", name="createsortie")
     */
    public function creerSortie(Request $req)
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        // récuperation des villes
        $repoVille = $this->getDoctrine()->getRepository(Ville::class);
        $villes = $repoVille->findAll();
        // récuperation du user pour affichage de ville organisatrice
        $user = $this->getUser();

        /* récuperation des lieux
        $repoLieu = $this->getDoctrine()->getRepository(Lieu::class);
        $allLieu = $repoLieu->findAll();*/

        /* TODO : récuperation de la ville selectionnée pour filtrer les lieux
        $lieu = $repoLieu->findBy(
            'ville' =>
        );*/

        // récupération de l'id de l'utilisateur pour remplir le champ "Organisateur"
        $sortie->setOrganisateur($this->getUser());

        $sortieForm->handleRequest($req);

        // enregistrement
        if ($sortieForm->isSubmitted() && $sortieForm->isValid() && $req->request->get('enregistrer')) {
            $em = $this->getDoctrine()->getManager();
            $sortie->setEtat('En création');
            $sortie->setSiteOrg($this->getUser()->getUserSite());
            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success', 'Votre sortie a bien été enregistrée.');
            return $this->redirectToRoute('home');
        }
        // publication
        if ($sortieForm->isSubmitted() && $sortieForm->isValid() && $req->request->get('publier')) {

            $currentDatetime = new \DateTime('now');

            // date de cloture inscription supérieure a l'heure actuelle de 12h et inferieure à la date de sortie
            if($sortie->getDateLimiteInscription() > $currentDatetime->modify('+ 12 hours') and $sortie->getDateLimiteInscription() < $sortie->getDateSortie())
            {
                $em = $this->getDoctrine()->getManager();
                $sortie->setEtat('Ouvert');
                $sortie->setSiteOrg($this->getUser()->getUserSite());
                $em->persist($sortie);
                $em->flush();

                $this->addFlash('success', 'Votre sortie a bien été publiée.');
                return $this->redirectToRoute('home');
            }
            else
            {
                $dateLimite = $currentDatetime->modify('+ 12 hours');
                $this->addFlash('danger', "La date limite d'inscription doit être supérieure à ". $dateLimite->format("Y-m-d H:i"). " être antérieure à la date de la sortie !");
            }

        }

        return $this->render('sortie/creer_sortie.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'villes' => $villes,
            'user' => $user,
            //'lieux' => $allLieu,
        ]);
    }

    /**
     * @Route("/sortie/update/{id}",
     *     name="updateSortie",
     *     requirements={"id": "\d+"},
     *     methods={"GET", "POST"}
     *     )
     */
    public function modifierSortie(int $id, Request $request)
    {
        $SortieRepository = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $SortieRepository->find($id);

        if($sortie->getOrganisateur() != $this->getUser())
        {
            $this->addFlash('danger', 'Cette sortie ne vous appartient pas !');
            return $this->redirectToRoute('home');
        }
        else
        {
            $sortieForm = $this->createForm(SortieType::class, $sortie);
            // récuperation des villes
            $repoVille = $this->getDoctrine()->getRepository(Ville::class);
            $villes = $repoVille->findAll();
            // récuperation du user pour affichage de ville organisatrice
            $user = $this->getUser();
            /* récuperation des lieux
            $repoLieu = $this->getDoctrine()->getRepository(Lieu::class);
            $allLieu = $repoLieu->findAll();*/

            $sortieForm->handleRequest($request);

            // enregistrement
            if ($sortieForm->isSubmitted() && $sortieForm->isValid() && $request->request->get('enregistrer')) {
                $em = $this->getDoctrine()->getManager();
                $sortie->setEtat('En création');
                $sortie->setSiteOrg($this->getUser()->getUserSite());
                $em->persist($sortie);
                $em->flush();

                $this->addFlash('success', 'Votre sortie a bien été enregistrée.');
                return $this->redirectToRoute('home');
            }
            // publication
            if ($sortieForm->isSubmitted() && $sortieForm->isValid() && $request->request->get('publier')) {
                $this->publierSortie($id);
            }
            // suppression
            if ($sortieForm->isSubmitted() && $request->request->get('supprimer')) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($sortie);
                $em->flush();
                $this->addFlash('success', 'Votre sortie a bien été supprimée.');
                return $this->redirectToRoute('home');
            }
        }

        return $this->render('sortie/modifier_sortie.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'sortie' => $sortie,
            'villes' => $villes,
            //'lieux' => $allLieu,
        ]);
    }

    /**
     * @Route("/sortie/publier/{id}",
     *     name="publier",
     *     requirements={"id": "\d+"},
     *     methods={"GET"}
     *     )
     */
    public function publierSortie(int $id)
    {
        $em = $this->getDoctrine()->getManager();
        $SortieRepository = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $SortieRepository->find($id);

        $currentDatetime = new \DateTime('now');

        if($sortie->getOrganisateur() != $this->getUser())
        {
            $this->addFlash('danger', 'Cette sortie ne vous appartient pas !');
            return $this->redirectToRoute('home');
        }
        else
        {
            // date de cloture inscription supérieure a l'heure actuelle de 12h et inferieure à la date de sortie
            if($sortie->getDateLimiteInscription() > $currentDatetime->modify('+ 12 hours') and $sortie->getDateLimiteInscription() < $sortie->getDateSortie())
            {
                // passe l'état de la sortie à "Ouvert"
                $sortie->setEtat("Ouvert");
                $em->persist($sortie);
                $em->flush();

                $this->addFlash('success', 'Votre sortie a bien été publiée.');
                return $this->redirectToRoute("home");
            }
            else
            {
                $dateLimite = $currentDatetime->modify('+ 12 hours');
                $this->addFlash('danger', "La date limite d'inscription doit être supérieure à ". $dateLimite->format("Y-m-d H:i"). " être antérieure à la date de la sortie !");
            }
        }
    }

    /**
     * @Route("/sortie/afficher/{id}",
     *     name="afficher",
     *     requirements={"id": "\d+"},
     *     methods={"GET", "POST"}
     *     )
     */
    public function afficherSortie(int $id)
    {
        $SortieRepository = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $SortieRepository->find($id);

        $participants = $sortie->getParticipants();

        if (!$sortie) {
            $this->addFlash('danger', "Cette sortie n'existe pas !");
            return $this->redirectToRoute('home');
        }

        return $this->render('sortie/details.html.twig', [
            "sortie" => $sortie,
            "participants" => $participants
        ]);

    }

    /**
     * @Route("/sortie/annuler/{id}",
     *     name="annuler",
     *     requirements={"id": "\d+"},
     *     methods={"GET", "POST"}
     *     )
     */
    public function annulerSortie(int $id, Request $request){

        $SortieRepository = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $SortieRepository->find($id);
        $annulation = new Annulation();

        if($this->getUser() != $sortie->getOrganisateur()){
            $this->addFlash('danger', 'Cette sortie ne vous appartient pas !');
            return $this->redirectToRoute('home');
        }
        if (!$sortie) {
            $this->addFlash('danger', "Cette sortie n'existe pas !");
            return $this->redirectToRoute('home');
        }

        if($request->request->get('motif')){
            $annulation->setSortie($sortie);
            $annulation->setMotif($request->request->get('motif'));
            $sortie->setEtat('Annulée');

            $em = $this->getDoctrine()->getManager();
            $em->persist($sortie);
            $em->persist($annulation);
            $em->flush();

            $this->addFlash('success', 'Votre sortie a bien été annulée !');
            return $this->redirectToRoute('home');
        }

        return $this->render('sortie/annuler.html.twig',[
            "sortie"=> $sortie
        ]);
    }

    /**
     * @Route("/sortie/inscrire/{id}",
     *      name="inscrire",
     *     requirements={"id": "\d+"},
     *     methods={"GET"})
     */
    public function inscrire(int $id)
    {
        $SortieRepository = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $SortieRepository->find($id);

        // on inscrit l'utilisateur que si le côtat le permet
        if(count($sortie->getParticipants()) < $sortie->getNbPlace())
        {
            $sortie->addParticipant($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($sortie);
            $em->flush();
        }
        else
        {
            $this->addFlash('danger', "Dommage il semblerait qu'il n'y est plus de place pour cette sortie ! :( ");
        }

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/sortie/desister/{id}",
     *     name="desister",
     *     requirements={"id": "\d+"},
     *     methods={"GET"}
     *     )
     */
    public function desister(int $id)
    {
        $sortieRepository = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $sortieRepository->find($id);

        if (!$sortie) {
            $this->addFlash('danger', "Cette sortie n'existe pas !");
            return $this->redirectToRoute('home');
        }

        // supprime l'utilisateur de la liste des participants
        $sortie->removeParticipant($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirectToRoute('home');
    }
}
