<?php

namespace App\Controller;

use App\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    /**
     * @Route("/site", name="site")
     */
    public function index(Request $request)
    {
        $SiteRepository = $this->getDoctrine()->getRepository(Site::class);
        $sites = $SiteRepository->findAll();

        if($request->request->get("site")){
            $sites = $SiteRepository->findByExampleField($request->request->get("site"));
        }

        if($request->request->get('newNom')){

            $SiteRepository = $this->getDoctrine()->getRepository(Ville::class);
            $site = $SiteRepository->find($request->request->get('id'));
            $site->setNom($request->request->get('newNom'));


            $em = $this->getDoctrine()->getManager();
            $em->persist($site);
            $em->flush();

            return $this->redirectToRoute('site');
        }

        if($request->request->get("nom")){

            $site = new Site();
            $site->setNom($request->request->get("nom"));
            $em = $this->getDoctrine()->getManager();
            $em->persist($site);
            $em->flush();

            return $this->redirectToRoute('site');
        }



        return $this->render('site/site.html.twig', [
            'sites' => $sites
        ]);
    }

    /**
     * @Route("/site/supprimer/{id}", name="supprSite",
     *     requirements= {"id":  "\d+"}
     * )
     */
    public function deleteVille(int $id){

        $SiteRepository = $this->getDoctrine()->getRepository(Site::class);
        $site = $SiteRepository->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($site);
        $em->flush();

        return $this->redirectToRoute('site');

    }
}
