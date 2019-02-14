<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/sortie/create", name="sortie")
     */
    public function creerSortie(Request $req)
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        // récupération de l'id de l'utilisateur pour remplir le champ "Organisateur"
        $sortie->setOrganisateur($this->getUser());
        $sortieForm->handleRequest($req);
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success', 'Votre sortie a bien été enregistrée.');
            return $this->redirectToRoute('default');
        }
        return $this->render('sortie/creer_sortie.html.twig', [
            'sortieForm' => $sortieForm->createView(),
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




    
}
