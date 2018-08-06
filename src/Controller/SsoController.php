<?php

namespace App\Controller;

use App\Entity\ServiceProvider;
use App\Security\Voter\ServiceProviderVoter;
use LightSaml\Bridge\Pimple\Container\BuildContainer;
use LightSaml\Idp\Builder\Profile\WebBrowserSso\Idp\SsoIdpReceiveAuthnRequestProfileBuilder;
use SchoolIT\LightSamlIdpBundle\Builder\Profile\WebBrowserSso\Idp\SsoIdpSendResponseProfileBuilderFactory;
use SchoolIT\LightSamlIdpBundle\RequestStorage\RequestStorageInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class SsoController extends Controller {

    /**
     * @Route("/idp/saml", name="idp_saml")
     */
    public function saml(RequestStorageInterface $requestStorage, SsoIdpReceiveAuthnRequestProfileBuilder $receiveBuilder, SsoIdpSendResponseProfileBuilderFactory $sendResponseBuilder) {
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
            throw new AccessDeniedHttpException();
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

        return $context->getHttpResponseContext()->getResponse();
    }
}