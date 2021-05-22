<?php

namespace App\Twig;

use League\CommonMark\MarkdownConverterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MarkdownExtension extends AbstractExtension {

    private $markdown;

    public function __construct(MarkdownConverterInterface $commonMarkConverter) {
        $this->markdown = $commonMarkConverter;
    }

    public function getFilters() {
        return [
            new TwigFilter('markdown', [$this, 'markdown'], ['is_safe' => ['html']])
        ];
    }

    public function markdown(string $input): string {
        return $this->markdown->convertToHtml($input);
    }
}