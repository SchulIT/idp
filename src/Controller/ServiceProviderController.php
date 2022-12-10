<?php

namespace App\Controller;

use App\Entity\SamlServiceProvider;
use App\Entity\ServiceProvider;
use App\Form\ServiceProviderType;
use App\Repository\ServiceProviderRepositoryInterface;
use Exception;
use LightSaml\Model\Metadata\EntityDescriptor;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/admin/service_providers')]
class ServiceProviderController extends AbstractController {

    public function __construct(private ServiceProviderRepositoryInterface $repository)
    {
    }

    #[Route(path: '', name: 'service_providers')]
    public function index(): Response {
        $serviceProviders = $this->repository
            ->findAll();

        return $this->render('service_providers/index.html.twig', [
            'service_providers' => $serviceProviders
        ]);
    }

    #[Route(path: '/{uuid}/certificate', name: 'service_provider_certificate')]
    public function certificateInfo(ServiceProvider $serviceProvider): Response {
        if(!$serviceProvider instanceof SamlServiceProvider) {
            return $this->redirectToRoute('service_providers');
        }

        $cert = openssl_x509_read($serviceProvider->getCertificate());
        $certificateInfo = openssl_x509_parse($cert);

        return $this->render('service_providers/info.html.twig', [
            'service_provider' => $serviceProvider,
            'certificate' => $certificateInfo
        ]);
    }

    #[Route(path: '/add', name: 'add_service_provider')]
    public function add(Request $request): Response {
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
            'type' => $serviceProvider::class
        ]);
    }

    #[Route(path: '/{uuid}/edit', name: 'edit_service_provider')]
    public function edit(Request $request, ServiceProvider $serviceProvider): Response {
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

    #[Route(path: '/{uuid}/remove', name: 'remove_service_provider')]
    public function remove(ServiceProvider $serviceProvider, Request $request, TranslatorInterface $translator): Response {
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

    /**
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    #[Route(path: '/metadata', name: 'load_xml_metadata')]
    public function loadXml(Request $request, HttpClientInterface $httpClient): Response {
        $url = $request->query->get('url');

        if(empty($url)) {
            throw new BadRequestException();
        }

        $response = $httpClient->request('GET', $url);
        if($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            throw new Exception(sprintf('Request was not successful. Got status %d', $response->getStatusCode()));
        }

        $xml = $response->getContent();
        $descriptor = EntityDescriptor::loadXml($xml);
        $certificate = null;
        $acs = null;

        foreach($descriptor->getAllSpKeyDescriptors() as $spKeyDescriptor) {
            if($spKeyDescriptor->getUse() === 'encryption') {
                $certificate = $spKeyDescriptor->getCertificate()->toPem();
            }
        }

        $sspSsoDescriptor = $descriptor->getFirstSpSsoDescriptor();

        if($sspSsoDescriptor !== null && $sspSsoDescriptor->getFirstAssertionConsumerService() !== null) {
            $acs = $sspSsoDescriptor->getFirstAssertionConsumerService()->getLocation();
        }

        return new JsonResponse([
            'entity_id' => $descriptor->getEntityID(),
            'certificate' => $certificate,
            'acs' => $acs
        ]);
    }
}