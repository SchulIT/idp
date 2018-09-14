<?php

namespace App\Controller;

use App\Entity\ServiceProvider;
use App\Saml\AttributeValueProvider;
use App\Security\Voter\ServiceProviderVoter;
use LightSaml\Binding\SamlPostResponse;
use LightSaml\Bridge\Pimple\Container\BuildContainer;
use LightSaml\Idp\Builder\Profile\WebBrowserSso\Idp\SsoIdpReceiveAuthnRequestProfileBuilder;
use SchoolIT\LightSamlIdpBundle\Builder\Profile\WebBrowserSso\Idp\SsoIdpSendResponseProfileBuilderFactory;
use SchoolIT\LightSamlIdpBundle\RequestStorage\RequestStorageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SsoController extends Controller {

    /**
     * @Route("/idp/saml", name="idp_saml")
     */
    public function saml(RequestStorageInterface $requestStorage, AttributeValueProvider $attributeValueProvider, SsoIdpReceiveAuthnRequestProfileBuilder $receiveBuilder, SsoIdpSendResponseProfileBuilderFactory $sendResponseBuilder) {
        $requestStorage->load();

        /** @var BuildContainer $buildContext */
        $buildContext = $this->get('lightsaml.container.build');

        $context = $receiveBuilder->buildContext();
        $action = $receiveBuilder->buildAction();

        $action->execute($context);

        $partyContext = $context->getPartyEntityContext();
        $endpoint = $context->getEndpoint();
        $message = $context->getInboundMessage();

        // check authorization
        /** @var ServiceProvider $serviceProvider */
        $serviceProvider = $this->getDoctrine()->getManager()
            ->getRepository(ServiceProvider::class)
            ->findOneByEntityId($partyContext->getEntityId());

        if(!$this->isGranted(ServiceProviderVoter::ENABLED, $serviceProvider)) {
            $requestStorage->clear();

            return $this->render('sso/denied.html.twig', [
                'service' => $serviceProvider
            ], new Response(null, Response::HTTP_FORBIDDEN));
        }

        $sendBuilder = $sendResponseBuilder->build(
            [new \LightSaml\Idp\Builder\Action\Profile\SingleSignOn\Idp\SsoIdpAssertionActionBuilder($buildContext)],
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

        if($response instanceof SamlPostResponse) {
            $data = $response->getData();
            $destination = $response->getDestination();
            $attributes = $attributeValueProvider->getValuesForUser($this->getUser(), $serviceProvider->getEntityId());

            return $this->render('sso/redirect_post.html.twig', [
                'service' => $serviceProvider,
                'data' => $data,
                'destination' => $destination,
                'attributes' => $attributes
            ]);
        } elseif($response instanceof RedirectResponse) {
            $destination = $response->getTargetUrl();

            $attributes = $attributeValueProvider->getValuesForUser($this->getUser(), $serviceProvider->getEntityId());

            return $this->render('sso/redirect_uri.html.twig', [
                'service' => $serviceProvider,
                'destination' => $destination,
                'attributes' => $attributes
            ]);
        }

        throw new \RuntimeException('Unsupported Binding!');
    }
}