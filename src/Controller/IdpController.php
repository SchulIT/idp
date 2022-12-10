<?php

namespace App\Controller;

use LightSaml\Model\Context\SerializationContext;
use LightSaml\Provider\EntityDescriptor\EntityDescriptorProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/idp')]
class IdpController extends AbstractController {
    public function __construct(private string $kernelProjectDir)
    {
    }
    #[Route(path: '', name: 'idp_details')]
    public function index(EntityDescriptorProviderInterface $provider): Response {
        $context = new SerializationContext();
        $own = $provider->get();
        $own->serialize($context->getDocument(), $context);
        $context->getDocument()->formatOutput = true;
        $idpXml = $context->getDocument()->saveXML();

        $cert = openssl_x509_read(file_get_contents($this->kernelProjectDir . '/certs/idp.crt'));
        $certificateInfo = openssl_x509_parse($cert);

        $loginUrl = $this->generateUrl('idp_saml');

        return $this->render('idp/index.html.twig', [
            'idpXml' => $idpXml,
            'loginUrl' => $loginUrl,
            'certificate' => $certificateInfo
        ]);
    }
}