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
        $ville = $repoVille->findAll();
        // récuperation du user pour affichage de ville organisatrice
        $user = $this->getUser();
        // récuperation des lieux
        $repoLieu = $this->getDoctrine()->getRepository(Lieu::class);
        $allLieu = $repoLieu->findAll();

        /* TODO : récuperation de la ville selectionnée pour filtrer les lieux
        $lieu = $repoLieu->findBy(
            'ville' =>
        );*/

        // récupération de l'id de l'utilisateur pour remplir le champ "Organisateur"
        $sortie->setOrganisateur($this->getUser());
        $sortieForm->handleRequest($req);
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $sortie->setEtat('En création');
            $sortie->setSiteOrg($this->getUser()->getUserSite());
            $em->persist($sortie);
            $em->flush();

            //$this->addFlash('success', 'Votre sortie a bien été enregistrée.');
            return $this->redirectToRoute('home');
        }
        return $this->render('sortie/creer_sortie.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'villes' => $ville,
            'user' => $user,
            'lieus' => $allLieu,
        ]);
    }

    /**
     * @Route("/sortie/afficher/{id}",
     *     name="afficher",
     *     requirements={"id": "\d+"},
     *     methods={"GET", "POST"}
     *     )
     */
    public function afficherSortie(int $id){

        $SortieRepository = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $SortieRepository->find($id);

        $participants = $sortie->getParticipants();

        if (!$sortie) {

            throw $this->createNotFoundException("Cette sortie n'existe pas !");
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
            throw $this->createAccessDeniedException("Cette sortie ne vous appartient pas !");
        }
        if (!$sortie) {

            throw  $this->createNotFoundException("Cette sortie n'existe pas !");

        }

        if($request->request->get('motif')){
            $annulation->setSortie($sortie);
            $annulation->setMotif($request->request->get('motif'));
            $sortie->setEtat('Annulée');

            $em = $this->getDoctrine()->getManager();
            $em->persist($sortie);
            $em->persist($annulation);
            $em->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('sortie/annuler.html.twig',[
            "sortie"=> $sortie
        ]);
    }

    /**
     * @Route("/sortie/inscrire", name="inscrire",
     * methods={"POST"})
     */
    public function inscrire(Request $request)
    {
        $SortieRepository = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $SortieRepository->find($request->request->get('id_sortie'));

        $sortie->addParticipant($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->persist($sortie);
        $em->flush();

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

        // supprime l'utilisateur de la liste des participants
        $sortie->removeParticipant($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirectToRoute('home');
    }
}
