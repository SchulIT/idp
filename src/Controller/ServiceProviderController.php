<?php

namespace App\Controller;

use App\Entity\SamlServiceProvider;
use App\Entity\ServiceProvider;
use App\Form\ServiceProviderType;
use App\Repository\ServiceProviderRepositoryInterface;
use App\Service\ServiceProviderTokenGenerator;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/service_providers")
 */
class ServiceProviderController extends AbstractController {

    private $repository;

    public function __construct(ServiceProviderRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * @Route("", name="service_providers")
     */
    public function index() {
        $serviceProviders = $this->repository
            ->findAll();

        return $this->render('service_providers/index.html.twig', [
            'service_providers' => $serviceProviders
        ]);
    }

    /**
     * @Route("/{uuid}/certificate", name="service_provider_certificate")
     */
    public function certificateInfo(ServiceProvider $serviceProvider) {
        if(!$serviceProvider instanceof SamlServiceProvider) {
            return $this->redirectToRoute('service_providers');
        }

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
        if($request->query->get('type', 'default') === 'saml') {
            $serviceProvider = new SamlServiceProvider();
        } else {
            $serviceProvider = new ServiceProvider();
        }

        $form = $this->createForm(ServiceProviderType::class, $serviceProvider);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($serviceProvider);

            $this->addFlash('success', 'service_providers.add.success');
            return $this->redirectToRoute('service_providers');
        }

        return $this->render('service_providers/add.html.twig', [
            'form' => $form->createView(),
            'type' => get_class($serviceProvider)
        ]);
    }

    /**
     * @Route("/{uuid}/edit", name="edit_service_provider")
     */
    public function edit(Request $request, ServiceProvider $serviceProvider) {
        $form = $this->createForm(ServiceProviderType::class, $serviceProvider);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($serviceProvider);

            $this->addFlash('success', 'service_providers.edit.success');
            return $this->redirectToRoute('service_providers');
        }

        return $this->render('service_providers/edit.html.twig', [
            'form' => $form->createView(),
            'service_provider' => $serviceProvider
        ]);
    }

    /**
     * @Route("/{uuid}/remove", name="remove_service_provider")
     */
    public function remove(ServiceProvider $serviceProvider, Request $request, TranslatorInterface $translator) {
        $form = $this->createForm(ConfirmType::class, [], [
            'message' => $translator->trans('service_providers.remove.confirm', [
                '%name%' => $serviceProvider->getName()
            ]),
            'label' => 'service_providers.remove.label'
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($serviceProvider);

            $this->addFlash('success', 'service_providers.remove.success');
            return $this->redirectToRoute('service_providers');
        }

        return $this->render('service_providers/remove.html.twig', [
            'form' => $form->createView(),
            'service_provider' => $serviceProvider
        ]);
    }
}