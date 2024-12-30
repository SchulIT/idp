<?php

namespace App\Controller;

use Exception;
use LightSaml\Idp\Builder\Action\Profile\SingleSignOn\Idp\SsoIdpAssertionActionBuilder;
use RuntimeException;
use App\Entity\SamlServiceProvider;
use App\Entity\User;
use App\Repository\ServiceProviderRepositoryInterface;
use App\Saml\AttributeValueProvider;
use App\Security\Voter\ServiceProviderVoter;
use App\Service\ServiceProviderConfirmationService;
use LightSaml\Binding\SamlPostResponse;
use LightSaml\Build\Container\BuildContainerInterface;
use LightSaml\Idp\Builder\Profile\WebBrowserSso\Idp\SsoIdpReceiveAuthnRequestProfileBuilder;
use SchulIT\LightSamlIdpBundle\Builder\Profile\WebBrowserSso\Idp\SsoIdpSendResponseProfileBuilderFactory;
use SchulIT\LightSamlIdpBundle\RequestStorage\RequestStorageInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class SsoController extends AbstractController {

    private const CSRF_TOKEN_ID = '_confirmation_token';

    public function __construct(private readonly ServiceProviderConfirmationService $confirmationService)
    {
    }

    #[Route(path: '/idp/saml', name: 'idp_saml')]
    public function saml(#[CurrentUser] User $user, RequestStorageInterface $requestStorage, AttributeValueProvider $attributeValueProvider,
                         SsoIdpReceiveAuthnRequestProfileBuilder $receiveBuilder,
                         SsoIdpSendResponseProfileBuilderFactory $sendResponseBuilder,
                         CsrfTokenManagerInterface $tokenManager, ServiceProviderRepositoryInterface $serviceProviderRepository,
                         BuildContainerInterface $buildContainer): Response {
        if($requestStorage->has() !== true) {
            return $this->redirectToRoute('dashboard');
        }

        if($user->getType()->isCanLinkStudents() && count($user->getLinkedStudents()) === 0) {
            $this->addFlash('error', 'sso.error.registration_incomplete');
            $requestStorage->clear();
            return $this->redirectToRoute('dashboard');
        }

        try {
            $requestStorage->load();

            $context = $receiveBuilder->buildContext();
            $action = $receiveBuilder->buildAction();

            $action->execute($context);

            $partyContext = $context->getPartyEntityContext();
            $endpoint = $context->getEndpoint();
            $message = $context->getInboundMessage();

            // check authorization
            $serviceProvider = $serviceProviderRepository
                ->findOneByEntityId($partyContext->getEntityId());

            if ($serviceProvider === null || !$serviceProvider instanceof SamlServiceProvider) {
                throw new BadRequestHttpException('The issusing service provider does not exist.');
            }

            if (!$this->isGranted(ServiceProviderVoter::ENABLED, $serviceProvider)) {
                $requestStorage->clear();

                return $this->render('sso/denied.html.twig', [
                    'service' => $serviceProvider
                ], new Response(null, Response::HTTP_FORBIDDEN));
            }

            $sendBuilder = $sendResponseBuilder->build(
                [new SsoIdpAssertionActionBuilder($buildContainer)],
                $partyContext->getEntityDescriptor()->getEntityID()
            );
            $sendBuilder->setPartyEntityDescriptor($partyContext->getEntityDescriptor());
            $sendBuilder->setPartyTrustOptions($partyContext->getTrustOptions());
            $sendBuilder->setEndpoint($endpoint);
            $sendBuilder->setMessage($message);

            $context = $sendBuilder->buildContext();
            $action = $sendBuilder->buildAction();

            $action->execute($context);

            $requestStorage->clear();

            $response = $context->getHttpResponseContext()->getResponse();

            $type = $this->confirmationService->needsConfirmation($user, $serviceProvider) ? 'confirm' : 'redirect';
            $token = $tokenManager->getToken(self::CSRF_TOKEN_ID);

            if ($response instanceof SamlPostResponse) {
                $data = $response->getData();
                $destination = $response->getDestination();
                $attributes = $attributeValueProvider->getValuesForUser($user, $serviceProvider->getEntityId());

                return $this->render('sso/' . $type . '_post.html.twig', [
                    'service' => $serviceProvider,
                    'data' => $data,
                    'destination' => $destination,
                    'attributes' => $attributes,
                    'csrf_token' => $token->getValue()
                ]);
            } else {
                if ($response instanceof RedirectResponse) {
                    $destination = $response->getTargetUrl();

                    $attributes = $attributeValueProvider->getValuesForUser($user, $serviceProvider->getEntityId());

                    return $this->render('sso/' . $type . '_uri.html.twig', [
                        'service' => $serviceProvider,
                        'destination' => $destination,
                        'attributes' => $attributes,
                        'csrf_token' => $token->getValue()
                    ]);
                }
            }

            throw new RuntimeException('Unsupported Binding!');
        } catch (Exception $e) {
            $this->addFlash('error', $e->getMessage());
        } finally {
            $requestStorage->clear();
        }

        return $this->redirectToRoute('dashboard');
    }

    #[Route(path: '/idp/saml/confirm/{uuid}', name: 'confirm_redirect')]
    public function confirm(#[CurrentUser] User $user, Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] SamlServiceProvider $serviceProvider, AttributeValueProvider $attributeValueProvider, CsrfTokenManagerInterface $tokenManager): Response {
        $type = $request->request->get('type');
        $destination = $request->request->get('destination');
        $data = $request->request->all('data');
        $token = $request->request->get('_csrf_token');

        if($this->isCsrfTokenValid(self::CSRF_TOKEN_ID, $token) !== true) {
            $token = $tokenManager->refreshToken(self::CSRF_TOKEN_ID);

            if ($type === 'post') {
                $attributes = $attributeValueProvider->getValuesForUser($user, $serviceProvider->getEntityId());

                return $this->render('sso/confirm_post.html.twig', [
                    'service' => $serviceProvider,
                    'data' => $data,
                    'destination' => $destination,
                    'attributes' => $attributes,
                    'csrf_token' => $token->getValue()
                ]);
            } else if ($type === 'redirect') {
                $attributes = $attributeValueProvider->getValuesForUser($user, $serviceProvider->getEntityId());

                return $this->render('sso/confirm_uri.html.twig', [
                    'service' => $serviceProvider,
                    'destination' => $destination,
                    'attributes' => $attributes,
                    'csrf_token' => $token->getValue()
                ]);
            }
        } else {
            $this->confirmationService->saveConfirmation($user, $serviceProvider);

            if($type === 'post') {
                return $this->render('sso/redirect_post.html.twig', [
                    'service' => $serviceProvider,
                    'data' => $data,
                    'destination' => $destination
                ]);
            } else if($type === 'redirect') {
                $token = $tokenManager->refreshToken(self::CSRF_TOKEN_ID);
                return $this->render('sso/redirect_uri.html.twig', [
                    'service' => $serviceProvider,
                    'destination' => $destination,
                    'csrf_token' => $token->getValue()
                ]);
            }
        }

        return $this->redirectToRoute('dashboard');
    }
}