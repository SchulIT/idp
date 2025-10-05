<?php

declare(strict_types=1);

namespace App\Controller;

use LightSaml\Model\Context\SerializationContext;
use LightSaml\Provider\EntityDescriptor\EntityDescriptorProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IdpController extends AbstractController
{
    public function __construct(private readonly string $kernelProjectDir)
    {
    }
    #[Route(path: '/admin/idp', name: 'idp_details')]
    public function index(EntityDescriptorProviderInterface $provider): Response {
        $context = new SerializationContext();
        $own = $provider->get();
        $own->serialize($context->getDocument(), $context);
        $context->getDocument()->formatOutput = true;
        $idpXml = $context->getDocument()->saveXML();

        $certAsString = file_get_contents($this->kernelProjectDir . '/certs/idp.crt');
        $cert = openssl_x509_read($certAsString);
        $certificateInfo = openssl_x509_parse($cert);

        $loginUrl = $this->generateUrl('idp_saml');

        return $this->render('idp/index.html.twig', [
            'idpXml' => $idpXml,
            'loginUrl' => $loginUrl,
            'certificate' => $certificateInfo,
            'cert' => $certAsString
        ]);
    }
    #[Route(path: '/admin/idp/xml', name: 'download_idp_xml')]
    public function downloadXml(EntityDescriptorProviderInterface $provider): Response {
        $context = new SerializationContext();
        $own = $provider->get();
        $own->serialize($context->getDocument(), $context);
        $context->getDocument()->formatOutput = true;
        $idpXml = $context->getDocument()->saveXML();

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            'idp.xml'
        );

        $response = new Response($idpXml, \Symfony\Component\HttpFoundation\Response::HTTP_OK, []);
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
