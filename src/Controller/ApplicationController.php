<?php

namespace App\Controller;

use App\Entity\Application;
use App\Form\ApplicationType;
use App\Repository\ApplicationRepositoryInterface;
use App\Service\ApplicationKeyGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/applications")
 */
class ApplicationController extends Controller {

    private $repository;

    public function __construct(ApplicationRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * @Route("", name="applications")
     */
    public function index() {
        $applications = $this->repository->findAll();

        return $this->render('applications/index.html.twig', [
            'applications' => $applications
        ]);
    }

    /**
     * @Route("/add", name="add_application")
     */
    public function add(Request $request, ApplicationKeyGenerator $keyGenerator) {
        $application = (new Application());

        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $application->setApiKey($keyGenerator->generateApiKey());

            $this->repository->persist($application);

            return $this->redirectToRoute('applications');
        }

        return $this->render('applications/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit_application")
     */
    public function edit(Application $application, Request $request) {
        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($application);

            return $this->redirectToRoute('applications');
        }

        return $this->render('applications/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/remove", name="remove_application")
     */
    public function remove() {

    }
}