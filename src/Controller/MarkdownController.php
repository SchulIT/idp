<?php

declare(strict_types=1);

namespace App\Controller;

use League\CommonMark\MarkdownConverterInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class MarkdownController extends AbstractController {

    #[Route(path: '/xhr/markdown', name: 'markdown_preview')]
    public function preview(Request $request, MarkdownConverterInterface $converter): Response {
        $markdown = $request->getContent();

        return new Response($converter->convertToHtml($markdown));
    }
}
