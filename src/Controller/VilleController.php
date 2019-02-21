<?php

namespace App\Controller;

use App\Entity\Ville;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VilleController extends AbstractController
{
    /**
     * @Route("/ville", name="ville"),
     * methods{"GET","POST"}
     */
    public function index(Request $request)
    {

        $VilleRepository = $this->getDoctrine()->getRepository(Ville::class);
        $villes = $VilleRepository->findAll();

        if($request->request->get("ville")){
            $villes = $VilleRepository->findByExampleField($request->request->get("ville"));
        }

        if($request->request->get('newNom') && $request->request->get('newCp')){

            $VilleRepository = $this->getDoctrine()->getRepository(Ville::class);
            $ville = $VilleRepository->find($request->request->get('id'));
            $ville->setNom($request->request->get('newNom'));
            $ville->setCodePostal($request->request->get('newCp'));


            $em = $this->getDoctrine()->getManager();
            $em->persist($ville);
            $em->flush();

            return $this->redirectToRoute('ville');
        }

        if($request->request->get("nom") && $request->request->get("cp")){

            $ville = new Ville();
            $ville->setNom($request->request->get("nom"));
            $ville->setCodePostal($request->request->get("cp"));
            $em = $this->getDoctrine()->getManager();
            $em->persist($ville);
            $em->flush();

            return $this->redirectToRoute('ville');
        }



        return $this->render('ville/ville.html.twig', [
            'controller_name' => 'VilleController',
            'villes' => $villes
        ]);
    }

    /**
     * @Route("/ville/supprimer/{id}", name="supprVille",
     *     requirements= {"id":  "\d+"}
     * )
     */
    public function deleteVille(int $id){

        $VilleRepository = $this->getDoctrine()->getRepository(Ville::class);
        $ville = $VilleRepository->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($ville);
        $em->flush();

        return $this->redirectToRoute('ville');

    }

}
