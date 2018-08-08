<?php

namespace App\Controller;

use App\Entity\ActiveDirectorySyncOption;
use App\Form\ActiveDirectorySyncOptionType;
use SchoolIT\CommonBundle\Form\ConfirmType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @Route("/admin/ad_sync")
 */
class ActiveDirectorySyncOptionController extends Controller {
    /**
     * @Route("", name="ad_sync_options")
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
     * @Route("/add", name="add_ad_sync_option")
     */
    public function add(Request $request) {
        $syncOption = new ActiveDirectorySyncOption();

        $form = $this->createForm(ActiveDirectorySyncOptionType::class, $syncOption);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($syncOption);
            $em->flush();

            $this->addFlash('success', 'ad_sync_options.add.success');
            return $this->redirectToRoute('ad_sync_options');
        }

        return $this->render('ad_sync_options/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit_ad_sync_option")
     */
    public function edit(Request $request, ActiveDirectorySyncOption $syncOption) {
        $form = $this->createForm(ActiveDirectorySyncOptionType::class, $syncOption);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($syncOption);
            $em->flush();

            $this->addFlash('success', 'ad_sync_options.edit.success');
            return $this->redirectToRoute('ad_sync_options');
        }

        return $this->render('ad_sync_options/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/remove", name="remove_ad_sync_option")
     */
    public function remove(ActiveDirectorySyncOption $option, Request $request, TranslatorInterface $translator) {
        $form = $this->createForm(ConfirmType::class, [], [
            'message' => $translator->trans('ad_sync_options.remove.confirm', [
                '%name%' => $option->getName()
            ]),
            'label' => 'ad_sync_options.remove.label'
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($option);
            $em->flush();

            $this->addFlash('success', 'ad_sync_options.remove.success');
            return $this->redirectToRoute('ad_sync_options');
        }

        return $this->render('ad_sync_options/remove.html.twig', [
            'form' => $form->createView(),
            'option' => $option
        ]);
    }
}