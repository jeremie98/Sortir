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
     * @Route("/sortie", name="sortie")
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

    
}
