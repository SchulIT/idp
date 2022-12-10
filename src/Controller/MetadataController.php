<?php

namespace App\Controller;

use LightSaml\Model\Context\SerializationContext;
use LightSaml\Provider\EntityDescriptor\EntityDescriptorProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MetadataController extends AbstractController {

    #[Route(path: '/metadata', name: 'xml_metadata')]
    public function xml(EntityDescriptorProviderInterface $provider): Response {
        $context = new SerializationContext();
        $own = $provider->get();
        $own->serialize($context->getDocument(), $context);
        $context->getDocument()->formatOutput = true;
        $idpXml = $context->getDocument()->saveXML();

        return new Response($idpXml, Response::HTTP_OK, [
            'Content-Type' => 'application/xml'
        ]);
    }
}