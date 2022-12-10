<?php

namespace App\Twig;

use League\CommonMark\MarkdownConverterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MarkdownExtension extends AbstractExtension {

    public function __construct(private MarkdownConverterInterface $converter)
    {
    }

    public function getFilters(): array {
        return [
            new TwigFilter('markdown_to_html', [ $this, 'markdown'], [ 'is_safe' => ['html'] ])
        ];
    }

    public function markdown(string $input): string {
        return $this->converter->convertToHtml($input);
    }
}