<?php

namespace App\Twig\Extension;

use App\Entity\User;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class HelperExtension extends AbstractExtension
{
    public function __construct(
        private Environment $twig,
        private TranslatorInterface $translator,
    ) {}

    public function getFilters(): array
    {
        return [
            new TwigFilter('json_decode', [$this, 'jsonDecode']),
            new TwigFilter('bool', [$this, 'bool']),
            new TwigFilter('ellipsis', [$this, 'ellipsis']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('goBack', [$this, 'goBack'], ['is_safe' => ['html']]),
            new TwigFunction('topTabs', [$this, 'topTabs'], ['is_safe' => ['html']]),
            new TwigFunction('breadcrumb', [$this, 'getBreadcrumb'], ['is_safe' => ['html']]),
            new TwigFunction('displayRole', [$this, 'displayRole'], ['is_safe' => ['html']]),
        ];
    }

    public function jsonDecode($string)
    {
        return json_decode($string, true);
    }

    public function bool(?bool $val): string {
        return match ($val) {
            null => 'null',
            true => 'true',
            false => 'false',
        };
    }

    public function goBack(string $url, string $title): string
    {
        return $this->twig->render('base/element/_go_back.html.twig', [
            'url' => $url,
            'title' => $title,
        ]);
    }

    public function topTabs(array $tabs): string
    {
        return $this->twig->render('base/element/_top_tabs.html.twig', [
            'tabs' => $tabs,
        ]);
    }

    public function getBreadcrumb(array $items): string
    {
        return $this->twig->render('base/element/_breadcrumb.html.twig', [
            'items' => $items,
        ]);
    }

    public function displayRole(User $user): string
    {
        return $this->twig->render('base/element/_roles.html.twig', [
            'user' => $user,
        ]);
    }


    public function ellipsis(string $text, int $length = 150): string
    {
        $dots = strlen($text) > $length ? '...' : '';

        return substr($text, 0, $length) . $dots;
    }

}