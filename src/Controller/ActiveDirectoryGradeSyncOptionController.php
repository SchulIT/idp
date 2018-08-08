<?php

namespace App\Controller;

use App\Entity\ActiveDirectoryGradeSyncOption;
use App\Form\ActiveDirectoryGradeSyncOptionType;
use SchoolIT\CommonBundle\Form\ConfirmType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @Route("/admin/ad_sync/grades")
 */
class ActiveDirectoryGradeSyncOptionController extends Controller {

    /**
     * @Route("", name="ad_grades_sync_options")
     */
    public function index() {
        $options = $this->getDoctrine()
            ->getRepository(ActiveDirectoryGradeSyncOption::class)
            ->findAll();

        return $this->render('ad_sync_options/grades/index.html.twig', [
            'sync_options' => $options
        ]);
    }

    /**
     * @Route("/add", name="add_ad_grades_sync_options")
     */
    public function add(Request $request) {
        $option = new ActiveDirectoryGradeSyncOption();

        $form = $this->createForm(ActiveDirectoryGradeSyncOptionType::class, $option);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($option);
            $em->flush();

            $this->addFlash('success', 'ad_sync_options.grades.add.success');
            return $this->redirectToRoute('ad_grades_sync_options');
        }

        return $this->render('ad_sync_options/grades/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit_ad_grades_sync_options")
     */
    public function edit(Request $request, ActiveDirectoryGradeSyncOption $option) {
        $form = $this->createForm(ActiveDirectoryGradeSyncOptionType::class, $option);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($option);
            $em->flush();

            $this->addFlash('success', 'ad_sync_options.grades.add.success');
            return $this->redirectToRoute('ad_grades_sync_options');
        }

        return $this->render('ad_sync_options/grades/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/remove", name="remove_ad_grades_sync_options")
     */
    public function remove(ActiveDirectoryGradeSyncOption $option, Request $request, TranslatorInterface $translator) {
        $form = $this->createForm(ConfirmType::class, [], [
            'message' => $translator->trans('ad_sync_options.grades.remove.confirm', [
                '%grade%' => $option->getGrade()
            ]),
            'label' => 'ad_sync_options.grades.remove.label'
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($option);
            $em->flush();

            $this->addFlash('success', 'ad_sync_options.grades.remove.success');
            return $this->redirectToRoute('ad_grades_sync_options');
        }

        return $this->render('ad_sync_options/grades/remove.html.twig', [
            'form' => $form->createView(),
            'option' => $option
        ]);
    }
}