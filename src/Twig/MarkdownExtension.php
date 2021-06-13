<?php

namespace App\Twig;

use League\CommonMark\MarkdownConverterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MarkdownExtension extends AbstractExtension {

    private $converter;

    public function __construct(MarkdownConverterInterface $converter) {
        $this->converter = $converter;
    }

    public function getFilters() {
        return [
            new TwigFilter('markdown_to_html', [ $this, 'markdown'], [ 'is_safe' => ['html'] ])
        ];
    }

    public function markdown(string $input): string {
        return $this->converter->convertToHtml($input);
    }
}