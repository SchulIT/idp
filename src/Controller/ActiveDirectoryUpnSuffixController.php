<?php

namespace App\Controller;

use App\Entity\ActiveDirectoryUpnSuffix;
use App\Form\ActiveDirectoryUpnSuffixType;
use App\Repository\ActiveDirectoryUpnSuffixRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/ad_sync/upn_suffixes")
 */
class ActiveDirectoryUpnSuffixController extends AbstractController {

    private $repository;

    public function __construct(ActiveDirectoryUpnSuffixRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * @Route("", name="ad_upn_suffixes")
     */
    public function index() {
        return $this->render('ad_sync_options/upn_suffixes/index.html.twig', [
            'suffixes' => $this->repository->findAll()
        ]);
    }

    /**
     * @Route("/add", name="add_ad_upn_suffixes")
     */
    public function add(Request $request) {
        $suffix = new ActiveDirectoryUpnSuffix();
        $form = $this->createForm(ActiveDirectoryUpnSuffixType::class, $suffix);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($suffix);

            $this->addFlash('success', 'ad_sync_options.upn_suffixes.add.success');

            return $this->redirectToRoute('ad_upn_suffixes');
        }

        return $this->render('ad_sync_options/upn_suffixes/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{uuid}/edit", name="edit_ad_upn_suffixes")
     */
    public function edit(ActiveDirectoryUpnSuffix $suffix, Request $request) {
        $form = $this->createForm(ActiveDirectoryUpnSuffixType::class, $suffix);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($suffix);

            $this->addFlash('success', 'ad_sync_options.upn_suffixes.edit.success');

            return $this->redirectToRoute('ad_upn_suffixes');
        }

        return $this->render('ad_sync_options/upn_suffixes/edit.html.twig', [
            'form' => $form->createView(),
            'suffix' => $suffix
        ]);
    }

    /**
     * @Route("/{uuid}/remove", name="remove_ad_upn_suffixes")
     */
    public function remove(ActiveDirectoryUpnSuffix $suffix, Request $request) {
        $form = $this->createForm(ActiveDirectoryUpnSuffixType::class, $suffix);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($suffix);

            $this->addFlash('success', 'ad_sync_options.upn_suffixes.remove.success');

            return $this->redirectToRoute('ad_upn_suffixes');
        }

        return $this->render('ad_sync_options/upn_suffixes/remove.html.twig', [
            'form' => $form->createView(),
            'suffix' => $suffix
        ]);
    }
}