<?php

namespace ThemeViz;

class TwigCompiler
{
    /** @var Twig $twig */
    private $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }
}