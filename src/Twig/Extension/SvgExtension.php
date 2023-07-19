<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SvgExtension extends AbstractExtension
{

    public function __construct(
        private string $projectDir,
    ){}

    public function getFilters(): array
    {
        return [
            new TwigFilter('svg', [$this, 'getSvg'], ['is_safe' => ['html']]),
        ];
    }

    /** Renvoi un svg valide ou sinon une image avec le lien SVG et le name en alt */
    public function getSvg($name): string
    {
        $svgPath = $this->projectDir . '/public/assets/svg/' . $name . '.svg';

        if (file_exists($svgPath)) {
            return file_get_contents($svgPath);
        }
        return sprintf(
            '<img src="%s" alt="%s">',
            $svgPath,
            $name,
        );
    }

}