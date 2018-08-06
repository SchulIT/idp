<?php

namespace App\Controller;

use App\Entity\ServiceProvider;
use App\Form\ServiceProviderType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/service_providers")
 */
class ServiceProviderController extends Controller {

    /**
     * @Route("", name="service_providers")
     */
    public function index() {
        $serviceProviders = $this->getDoctrine()
            ->getRepository(ServiceProvider::class)
            ->findAll();

        return $this->render('service_providers/index.html.twig', [
            'service_providers' => $serviceProviders
        ]);
    }

    /**
     * @Route("/{id}/certificate", name="service_provider_certificate")
     */
    public function certificateInfo(ServiceProvider $serviceProvider) {

        $cert = openssl_x509_read($serviceProvider->getCertificate());
        $certificateInfo = openssl_x509_parse($cert);
        openssl_x509_free($cert);

        return $this->render('service_providers/info.html.twig', [
            'service_provider' => $serviceProvider,
            'certificate' => $certificateInfo
        ]);
    }

    /**
     * @Route("/add", name="add_service_provider")
     */
    public function add(Request $request) {
        $serviceProvider = new ServiceProvider();

        $form = $this->createForm(ServiceProviderType::class, $serviceProvider);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($serviceProvider);
            $em->flush();

            return $this->redirectToRoute('service_providers');
        }

        return $this->render('service_providers/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit_service_provider")
     */
    public function edit(Request $request, ServiceProvider $serviceProvider) {
        $form = $this->createForm(ServiceProviderType::class, $serviceProvider);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($serviceProvider);
            $em->flush();

            return $this->redirectToRoute('service_providers');
        }

        return $this->render('service_providers/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/remove", name="remove_service_provider")
     */
    public function remove() {

    }
}