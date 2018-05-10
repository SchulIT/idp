<?php

namespace App\Controller;

use App\Entity\ActiveDirectorySyncOption;
use App\Form\ActiveDirectorySyncOptionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ActiveDirectorySyncOptionController extends Controller {
    /**
     * @Route("/admin/ad_sync", name="ad_sync_options")
     */
    public function index() {
        $syncOptions = $this->getDoctrine()->getManager()
            ->getRepository(ActiveDirectorySyncOption::class)
            ->findAll();

        return $this->render('ad_sync_options/index.html.twig', [
            'sync_options' => $syncOptions
        ]);
    }

    /**
     * @Route("/admin/ad_sync/add", name="add_ad_sync_option")
     */
    public function add(Request $request) {
        $syncOption = new ActiveDirectorySyncOption();

        $form = $this->createForm(ActiveDirectorySyncOptionType::class, $syncOption);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($syncOption);
            $em->flush();

            return $this->redirectToRoute('ad_sync_options');
        }

        return $this->render('ad_sync_options/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/ad_sync/{id}/edit", name="edit_ad_sync_option")
     */
    public function edit(Request $request, ActiveDirectorySyncOption $syncOption) {
        $form = $this->createForm(ActiveDirectorySyncOptionType::class, $syncOption);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($syncOption);
            $em->flush();

            return $this->redirectToRoute('ad_sync_options');
        }

        return $this->render('ad_sync_options/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/ad_sync/{id}/remove", name="remove_ad_sync_option")
     */
    public function remove() {

    }
}