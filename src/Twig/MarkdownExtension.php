<?php

declare(strict_types=1);

namespace App\Twig;

use League\CommonMark\ConverterInterface;
use Override;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MarkdownExtension extends AbstractExtension {

    public function __construct(private readonly ConverterInterface $converter)
    {
    }

    #[Override]
    public function getFilters(): array {
        return [
            new TwigFilter('markdown_to_html', $this->markdown(...), [ 'is_safe' => ['html'] ])
        ];
    }

    public function markdown(string $input): string {
        return $this->converter->convert($input)->getContent();
    }
}
