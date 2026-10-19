<?php

declare(strict_types=1);

namespace App\Controller;

use App\Autoconfig\AutoconfigService;
use App\Autoconfig\Configurator;
use App\Entity\OAuthClient;
use App\Entity\OidcServiceProvider;
use App\Entity\SamlServiceProvider;
use App\Entity\ServiceProvider;
use App\Form\ServiceProviderAutoconfigureType;
use App\Form\ServiceProviderType;
use App\Repository\ServiceProviderRepositoryInterface;
use App\Utils\SecurityUtils;
use Exception;
use League\Bundle\OAuth2ServerBundle\ValueObject\Grant;
use League\Bundle\OAuth2ServerBundle\ValueObject\Scope;
use LightSaml\Model\Metadata\EntityDescriptor;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Http\Attribute\NotFoundRedirect;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

class ServiceProviderController extends AbstractController
{
    public function __construct(private readonly ServiceProviderRepositoryInterface $repository)
    {
    }
    #[Route(path: '/admin/service_providers', name: 'service_providers')]
    public function index(): Response {
        $serviceProviders = $this->repository
            ->findAll();

        return $this->render('service_providers/index.html.twig', [
            'service_providers' => $serviceProviders
        ]);
    }

    #[NotFoundRedirect(redirectRoute: 'service_providers', flashMessage: 'service_providers.not_found')]
    #[Route(path: '/admin/service_providers/{uuid}/certificate', name: 'service_provider_certificate')]
    public function certificateInfo(#[MapEntity(mapping: ['uuid' => 'uuid'])] ServiceProvider $serviceProvider): Response {
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
    #[Route(path: '/admin/service_providers/add', name: 'add_service_provider')]
    public function add(
        Request $request,
        #[Autowire(service: 'league.oauth2_server.password_hasher')] PasswordHasherInterface $passwordHasher,
        TranslatorInterface $translator,
        #[MapQueryParameter] string|null $type = null
    ): Response {
        if($type === 'saml') {
            $serviceProvider = new SamlServiceProvider();
        } elseif($type === 'oidc') {
            $serviceProvider = new OidcServiceProvider();
            $client = new OAuthClient('', Uuid::uuid4()->toString(), '');
            $serviceProvider->setClient($client);
        } else {
            $serviceProvider = new ServiceProvider();
        }

        $form = $this->createForm(ServiceProviderType::class, $serviceProvider);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if($serviceProvider instanceof OidcServiceProvider) {
                $secret = SecurityUtils::getRandomHexString(128);
                $hashedSecret = $passwordHasher->hash($secret);

                $serviceProvider->getClient()->setName($serviceProvider->getName());
                $serviceProvider->getClient()->setSecret($hashedSecret);

                $serviceProvider->getClient()->setScopes(
                    new Scope('openid'),
                    new Scope('profile'),
                    new Scope('email'),
                    new Scope('groups'),
                    new Scope($serviceProvider->getClient()->getScopeName())
                );

                $serviceProvider->getClient()->setGrants(
                    new Grant('client_credentials'),
                    new Grant('authorization_code'),
                    new Grant('token'),
                    new Grant('id_token')
                );

                $this->repository->persist($serviceProvider);

                $this->addFlash('success', $translator->trans('service_providers.add.oidc_success', [
                    '%client_id%' => $serviceProvider->getClient()->getIdentifier(),
                    '%client_secret%' => $secret
                ]));
            } else {
                $this->repository->persist($serviceProvider);
                $this->addFlash('success', 'service_providers.add.success');
            }

            return $this->redirectToRoute('service_providers');
        }

        return $this->render('service_providers/add.html.twig', [
            'form' => $form->createView(),
            'type' => $serviceProvider::class
        ]);
    }

    #[Route(path: '/admin/service_providers/add/autoconfig', name: 'add_service_provider_autoconfig')]
    public function addAutoconfig(
        Request $request,
        Configurator $configurator,
        ValidatorInterface $validator,
        LoggerInterface $logger
    ): Response {
        $autoconfigService = new AutoconfigService();

        $form = $this->createForm(ServiceProviderAutoconfigureType::class, $autoconfigService);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            try {
                $serviceProvider = new SamlServiceProvider()
                    ->setAutoconfigureUrl($autoconfigService->autoconfigureUrl);
                $configurator->configure($serviceProvider);
                $violations = $validator->validate($serviceProvider);

                if(count($violations) > 0) {
                    foreach($violations as $violation) {
                        $logger->error($violation->getMessage());
                    }

                    $this->addFlash('error', 'service_providers.autoconfig.validation_failed');
                }

                $this->repository->persist($serviceProvider);

                $this->addFlash('success', 'service_providers.add.success');
                return $this->redirectToRoute('service_providers');
            } catch (Throwable $e) {
                $this->addFlash('error', sprintf('%s: %s', get_class($e), $e->getMessage()));
            }
        }

        return $this->render('service_providers/add_autoconfig.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[NotFoundRedirect(redirectRoute: 'service_providers', flashMessage: 'service_providers.not_found')]
    #[Route(path: '/admin/service_providers/{uuid}/edit', name: 'edit_service_provider')]
    public function edit(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] ServiceProvider $serviceProvider): Response {
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

    #[NotFoundRedirect(redirectRoute: 'service_providers', flashMessage: 'service_providers.not_found')]
    #[Route(path: '/admin/service_providers/{uuid}/remove', name: 'remove_service_provider')]
    public function remove(#[MapEntity(mapping: ['uuid' => 'uuid'])] ServiceProvider $serviceProvider, Request $request, TranslatorInterface $translator): Response {
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

    #[NotFoundRedirect(redirectRoute: 'service_providers', flashMessage: 'service_providers.not_found')]
    #[Route(path: '/admin/service_providers/{uuid}/autoconfig', name: 'autoconfigure_service_provider')]
    public function doAutoconfig(
        #[MapEntity(mapping: ['uuid' => 'uuid'])] ServiceProvider $serviceProvider,
        Configurator $configurator,

    ): RedirectResponse {
        if(!$serviceProvider instanceof SamlServiceProvider) {
            return $this->redirectToRoute('service_providers');
        }

        try {
            $configurator->configure($serviceProvider);
            $this->repository->persist($serviceProvider);

            $this->addFlash('success', 'service_providers.autoconfig.success');
            return $this->redirectToRoute('service_providers');
        } catch (Throwable $e) {
            $this->addFlash('error', sprintf('%s: %s', get_class($e), $e->getMessage()));
        }

        return $this->redirectToRoute('service_providers');
    }


    /**
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    #[Route(path: '/admin/service_providers/metadata', name: 'load_xml_metadata')]
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
            if($spKeyDescriptor->getUse() === 'encryption' || $spKeyDescriptor->getUse() === 'signing') {
                $certificate = $spKeyDescriptor->getCertificate()->toPem();
            }
        }

        $sspSsoDescriptor = $descriptor->getFirstSpSsoDescriptor();

        $acsUrls = [ ];

        if($sspSsoDescriptor !== null) {
            foreach($sspSsoDescriptor->getAllAssertionConsumerServices() as $acs) {
                if(!empty($acs->getLocation())) {
                    $acsUrls[] = $acs->getLocation();
                }
            }
        }

        $acsUrls = array_unique($acsUrls);

        return new JsonResponse([
            'entity_id' => $descriptor->getEntityID(),
            'certificate' => $certificate,
            'acsUrls' => $acsUrls
        ]);
    }
}
