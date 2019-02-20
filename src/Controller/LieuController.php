<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LieuController extends AbstractController
{
    /**
     * @Route("/lieu", name="lieu")
     */
    public function index(Request $request)
    {
        $villeRepository = $this->getDoctrine()->getRepository(Ville::class);
        $villes = $villeRepository->findAll();


        dd($request->request->get('ville'));


        if ($request->request->get('nom') && $request->request->get('rue')) {

            $lieuNom = $request->request->get('nom');
            $lieuRue = $request->request->get("rue");

            $ville = $villeRepository->find($request->request->get('ville'));
            // dd($request->request->get('ville'));

            $lieu = new Lieu();
            $lieu->setNom($lieuNom);
            $lieu->setRue($lieuRue);
            $lieu->setVille($ville);


            $em = $this->getDoctrine()->getManager();
            $em->persist($lieu);
            $em->flush();

            return $this->redirectToRoute('createsortie');
        }

        return $this->render('lieu/lieu.html.twig', ['villes' => $villes]);
    }
}
