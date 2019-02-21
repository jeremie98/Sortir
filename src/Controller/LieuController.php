<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuFormType;
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
        $lieu = new Lieu();
        $lieuForm = $this->createForm(LieuFormType::class, $lieu);
        $lieuForm->handleRequest($request);
        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($lieu);
            $em->flush();
            return $this->redirectToRoute('createsortie');
        }
        return $this->render('lieu/lieu.html.twig', ['lieuForm' => $lieuForm->createView()]);
    }
}
