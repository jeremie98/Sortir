<?php

namespace App\Controller;

use App\Entity\Groupe;
use App\Form\GroupeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GroupeController extends AbstractController
{
    /**
     * @Route("/groupe", name="groupe")
     */
    public function index(Request $request)
    {
        $groupe = new Groupe();
        $groupeForm = $this->createForm(GroupeType::class, $groupe);

        $user = $this->getUser();
        $groupeForm->handleRequest($request);

        // enregistrement
        if ($groupeForm->isSubmitted() && $groupeForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $groupe->setChef($user);
            $em->persist($groupe);
            $em->flush();

            $this->addFlash('success', 'Votre groupe a bien été enregistrée.');
            return $this->redirectToRoute('home');
        }


        return $this->render('user/groupe.html.twig', [
            'groupeForm' => $groupeForm->createView(),
        ]);
    }

    /**
     * @Route("/inscrireGroupe", name="inscrireGroupe")
     */
    public function inscrire()
    {

        $groupeRepo = $this->getDoctrine()->getRepository(Groupe::class);

        $groupes = $groupeRepo->findAll();

        //dd($groupes);


        return $this->render('user/inscrire.html.twig', [
            'groupes' => $groupes,
            'user' => $this->getUser()
        ]);

    }

    /**
     * @Route("/inscriptionValide/{id}",
     *     name="groupeInscrit",
     *     requirements={"id": "\d+"},
     *     methods={"GET"})
     */
    public function inscrireGroupe(int $id){

        $groupRepo = $this->getDoctrine()->getRepository(Groupe::class);
        $groupe = $groupRepo->find($id);

        $groupe->addParticipant($this->getUser());
        $this->addFlash('success', 'Vous a bien été inscrit au groupe : '. $groupe->getNom());

        return $this->redirectToRoute('home');
    }
}
