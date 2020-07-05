<?php

namespace App\Controller;

use App\Entity\ServiceProvider;
use App\Repository\ServiceProviderRepositoryInterface;
use App\Saml\AttributeValueProvider;
use App\Security\Voter\ServiceProviderVoter;
use App\Service\ServiceProviderConfirmationService;
use LightSaml\Binding\SamlPostResponse;
use LightSaml\Idp\Builder\Profile\WebBrowserSso\Idp\SsoIdpReceiveAuthnRequestProfileBuilder;
use LightSaml\SymfonyBridgeBundle\Bridge\Container\BuildContainer;
use SchulIT\LightSamlIdpBundle\Builder\Profile\WebBrowserSso\Idp\SsoIdpSendResponseProfileBuilderFactory;
use SchulIT\LightSamlIdpBundle\RequestStorage\RequestStorageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class SsoController extends AbstractController {

    private const CSRF_TOKEN_ID = '_confirmation_token';
    private $confirmationService;

    public function __construct(ServiceProviderConfirmationService $confirmationService) {
        $this->confirmationService = $confirmationService;
    }

    /**
     * @Route("/idp/saml", name="idp_saml")
     */
    public function saml(RequestStorageInterface $requestStorage, AttributeValueProvider $attributeValueProvider,
                         SsoIdpReceiveAuthnRequestProfileBuilder $receiveBuilder,
                         SsoIdpSendResponseProfileBuilderFactory $sendResponseBuilder,
                         CsrfTokenManagerInterface $tokenManager, ServiceProviderRepositoryInterface $serviceProviderRepository,
                         BuildContainer $buildContainer) {
        if($requestStorage->has() !== true) {
            return $this->redirectToRoute('dashboard');
        }

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

        if($serviceProvider === null) {
            throw new BadRequestHttpException('The issusing service provider does not exist.');
        }

        if(!$this->isGranted(ServiceProviderVoter::ENABLED, $serviceProvider)) {
            $requestStorage->clear();

            return $this->render('sso/denied.html.twig', [
                'service' => $serviceProvider
            ], new Response(null, Response::HTTP_FORBIDDEN));
        }

        $sendBuilder = $sendResponseBuilder->build(
            [new \LightSaml\Idp\Builder\Action\Profile\SingleSignOn\Idp\SsoIdpAssertionActionBuilder($buildContainer)],
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

        $type = $this->confirmationService->needsConfirmation($this->getUser(), $serviceProvider) ? 'confirm' : 'redirect';
        $token = $tokenManager->getToken(static::CSRF_TOKEN_ID);

        if ($response instanceof SamlPostResponse) {
            $data = $response->getData();
            $destination = $response->getDestination();
            $attributes = $attributeValueProvider->getValuesForUser($this->getUser(), $serviceProvider->getEntityId());

            return $this->render('sso/' . $type . '_post.html.twig', [
                'service' => $serviceProvider,
                'data' => $data,
                'destination' => $destination,
                'attributes' => $attributes,
                'csrf_token' => $token->getValue()
            ]);
        } else if ($response instanceof RedirectResponse) {
            $destination = $response->getTargetUrl();

            $attributes = $attributeValueProvider->getValuesForUser($this->getUser(), $serviceProvider->getEntityId());

            return $this->render('sso/' . $type . '_uri.html.twig', [
                'service' => $serviceProvider,
                'destination' => $destination,
                'attributes' => $attributes,
                'csrf_token' => $token->getValue()
            ]);
        }

        throw new \RuntimeException('Unsupported Binding!');
    }

    /**
     * @Route("/idp/saml/confirm/{uuid}", name="confirm_redirect")
     */
    public function confirm(Request $request, ServiceProvider $serviceProvider, AttributeValueProvider $attributeValueProvider, CsrfTokenManagerInterface $tokenManager) {
        $type = $request->request->get('type');
        $destination = $request->request->get('destination');
        $data = $request->request->get('data', [ ]);
        $token = $request->request->get('_csrf_token');

        if($this->isCsrfTokenValid(static::CSRF_TOKEN_ID, $token) !== true) {
            $token = $tokenManager->refreshToken(static::CSRF_TOKEN_ID);

            if ($type === 'post') {
                $attributes = $attributeValueProvider->getValuesForUser($this->getUser(), $serviceProvider->getEntityId());

                return $this->render('sso/confirm_post.html.twig', [
                    'service' => $serviceProvider,
                    'data' => $data,
                    'destination' => $destination,
                    'attributes' => $attributes,
                    'csrf_token' => $token->getValue()
                ]);
            } else if ($type === 'redirect') {
                $attributes = $attributeValueProvider->getValuesForUser($this->getUser(), $serviceProvider->getEntityId());

                return $this->render('sso/confirm_uri.html.twig', [
                    'service' => $serviceProvider,
                    'destination' => $destination,
                    'attributes' => $attributes,
                    'csrf_token' => $token->getValue()
                ]);
            }
        } else {
            $this->confirmationService->saveConfirmation($this->getUser(), $serviceProvider);

            if($type === 'post') {
                return $this->render('sso/redirect_post.html.twig', [
                    'service' => $serviceProvider,
                    'data' => $data,
                    'destination' => $destination
                ]);
            } else if($type === 'redirect') {
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