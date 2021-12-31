<?php

namespace App\Controller;

use League\CommonMark\MarkdownConverterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class MarkdownController extends AbstractController {

    /**
     * @Route("/xhr/markdown", name="markdown_preview")
     */
    public function preview(Request $request, MarkdownConverterInterface $converter): Response {
        $markdown = $request->getContent();

        return new Response($converter->convertToHtml($markdown));
    }
}